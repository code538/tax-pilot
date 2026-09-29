<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        if (!$user || !$user->is_super_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Super Admin access required.',
                'errors' => null,
            ], 403);
        }

        return $next($request);
    }
}
