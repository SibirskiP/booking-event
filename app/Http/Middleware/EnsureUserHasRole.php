<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // pustimo i varijantu gdje middleware pozivamo: 'role:organizer,admin'
        if (count($roles) === 1 && strpos($roles[0], ',') !== false) {
            $roles = explode(',', $roles[0]);
        }

        if (! in_array($user->role, $roles)) {
            return response()->json(['message' => 'Forbidden (insufficient role)'], 403);
        }

        return $next($request);
    }
}
