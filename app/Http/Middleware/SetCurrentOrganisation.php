<?php

namespace App\Http\Middleware;

use App\Models\Organisation;
use App\Services\OrganisationContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentOrganisation
{
    public function __construct(
        protected OrganisationContext $context
    ) {
    }

    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'errors' => null,
            ], 401);
        }

        $organisationId = $request->route(
            'organisationId'
        );

        if (!$organisationId) {
            return response()->json([
                'success' => false,
                'message' => 'Organisation ID is required.',
                'errors' => null,
            ], 400);
        }

        $organisation = Organisation::findOrFail(
            $organisationId
        );

        if (
            !$this->context->userCanAccess(
                $user,
                $organisation
            )
        ) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this organisation.',
                'errors' => null,
            ], 403);
        }

        $this->context->set($organisation);

        $request->attributes->set(
            'currentOrganisation',
            $organisation
        );

        return $next($request);
    }
}