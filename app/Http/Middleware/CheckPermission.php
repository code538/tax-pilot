<?php

namespace App\Http\Middleware;

use App\Services\OrganisationContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function __construct(
        protected OrganisationContext $context
    ) {
    }

    public function handle(
        Request $request,
        Closure $next,
        string $permission
    ): Response {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'errors' => null,
            ], 401);
        }

        /*
         * Super Admin bypasses organisation permissions.
         */
        if ($user->is_super_admin) {
            return $next($request);
        }

        /*
         * Get current organisation from Step 8.
         */
        $organisation = $this->context->require();

        /*
         * Check whether the user has the requested
         * permission inside this organisation.
         */
        $hasPermission = $user->roles()
            ->where('roles.organisation_id', $organisation->id)
            ->wherePivot(
                'organisation_id',
                $organisation->id
            )
            ->where('roles.is_active', true)
            ->whereHas('permissions', function ($query) use ($permission) {
                $query
                    ->where('permissions.slug', $permission)
                    ->where('permissions.is_active', true);
            })
            ->exists();

        if (!$hasPermission) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to perform this action.',
                'errors' => [
                    'permission' => $permission,
                ],
            ], 403);
        }

        return $next($request);
    }
}