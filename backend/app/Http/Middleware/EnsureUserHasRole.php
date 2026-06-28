<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Role-based access control: the "keycard" check from the concept.
 *
 * Usage in routes: ->middleware('role:provider') or 'role:provider,admin'.
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null || ! in_array($user->role->value, $roles, true)) {
            return response()->json([
                'message' => 'This action is not allowed for your role.',
            ], 403);
        }

        return $next($request);
    }
}
