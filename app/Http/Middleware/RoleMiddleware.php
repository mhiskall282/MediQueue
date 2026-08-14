<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\SecurityAlert;

/**
 * RoleMiddleware — Enforces role-based access control and least-privilege policies.
 *
 * Usage: Route::middleware('role:admin') or Route::middleware('role:staff,admin')
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

        // Must be authenticated
        if (!$user) {
            return redirect()->route('login');
        }

        // Must have an active account
        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been deactivated. Please contact the clinic.']);
        }

        // Staff Account Approval Gate
        if ($user->isStaff() && !$user->is_approved) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your clinical staff account is currently awaiting Administrator approval. You will receive an email notification once your license and role are verified.']);
        }

        // Expand 'staff' parameter to include all granular clinical roles
        $expandedRoles = [];
        foreach ($roles as $role) {
            if ($role === 'staff') {
                $expandedRoles = array_merge($expandedRoles, ['staff', 'doctor', 'nurse', 'pharmacist', 'lab_tech']);
            } else {
                $expandedRoles[] = $role;
            }
        }

        // Check if user's role is among the allowed roles
        if (!in_array($user->role, $expandedRoles, true)) {
            // Log security incident for ISO 27001 / HIPAA audit
            try {
                SecurityAlert::create([
                    'user_id'     => $user->id,
                    'event_type'  => 'UNAUTHORIZED_ROUTE_ACCESS_ATTEMPT',
                    'severity'    => SecurityAlert::SEVERITY_MEDIUM,
                    'description' => "User {$user->name} ({$user->role}) attempted unauthorized access to protected route: {$request->path()}",
                    'ip_address'  => $request->ip(),
                    'context_data'=> [
                        'required_roles' => $roles,
                        'user_role'      => $user->role,
                        'url'            => $request->fullUrl(),
                    ],
                ]);
            } catch (\Throwable $e) {
                // Fail silently to avoid breaking request
            }

            abort(403, 'Access denied. You do not have sufficient clinical/administrative privileges to access this resource.');
        }

        return $next($request);
    }
}
