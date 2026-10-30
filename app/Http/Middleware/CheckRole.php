<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        /** @var User|null $user */
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if (! in_array($user->role, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: You do not have sufficient permissions to access this resource.',
                'required_roles' => $roles,
                'your_role' => $user->role,
            ], 403);
        }

        // If user is store_admin, verify they have an assigned store
        if ($user->isStoreAdmin() && empty($user->store_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: Store Admin is not assigned to any store.',
            ], 403);
        }

        return $next($request);
    }
}
