<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RoleMiddleware — Enforces role-based access control.
 *
 * Usage: Route::middleware('role:admin') or Route::middleware('role:staff,admin')
 * Multiple roles can be passed as a comma-separated list.
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): Response $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Must be authenticated (belt-and-suspenders with auth middleware)
        if (!$user) {
            return redirect()->route('login');
        }

        // Must have an active account
        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been deactivated. Please contact the clinic.']);
        }

        // Check if user's role is among the allowed roles
        if (!in_array($user->role, $roles)) {
            abort(403, 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
