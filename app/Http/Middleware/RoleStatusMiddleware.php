<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleStatusMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (! in_array($user->role, $roles)) {
            return response()->json(['message' => 'Forbidden: wrong role'], 403);
        }

        if ($user->connected()->status === 'blocked') {
            return response()->json([
                'message' => 'Your account is blocked',
            ], 301);
        }

        if ($user->connected()->status !== 'active') {
            return response()->json(['message' => 'Your account is not active'], 403);
        }

        return $next($request);
    }
}
