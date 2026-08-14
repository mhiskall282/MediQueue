<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Mail\QueueNotificationMail;
use App\Models\AuditLog;
use App\Models\SecurityAlert;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();
        $previousIp = $user->last_login_ip;
        $currentIp  = $request->ip();

        // Detect IP change / New device login for HIPAA & ISO 27001
        if ($previousIp && $previousIp !== $currentIp) {
            try {
                if ($user->email_notifications_enabled) {
                    Mail::to($user->email)->send(new QueueNotificationMail(
                        $user,
                        'Security Notice: New Sign-in Detected',
                        'New Location Sign-In Alert',
                        "A new sign-in to your MediQueue account occurred from IP: {$currentIp} (Previous known IP: {$previousIp}). If this was you, no action is needed. If you did not initiate this session, please update your password immediately.",
                        [
                            'Current IP'     => $currentIp,
                            'Previous IP'    => $previousIp,
                            'Time of Login'  => now()->toDateTimeString(),
                            'Device/Browser' => substr($request->userAgent() ?? 'Unknown Browser', 0, 80),
                        ]
                    ));
                }

                SecurityAlert::create([
                    'user_id'     => $user->id,
                    'event_type'  => 'NEW_IP_SIGNIN_DETECTED',
                    'severity'    => SecurityAlert::SEVERITY_LOW,
                    'description' => "User {$user->name} signed in from new IP: {$currentIp} (Previous: {$previousIp})",
                    'ip_address'  => $currentIp,
                ]);
            } catch (\Throwable $e) {}
        }

        // Update login telemetry
        $user->update([
            'last_login_ip' => $currentIp,
            'last_login_at' => now(),
        ]);

        AuditLog::create([
            'user_id'     => $user->id,
            'action'      => 'auth.login',
            'entity_type' => 'User',
            'entity_id'   => $user->id,
            'metadata'    => ['ip' => $currentIp],
            'ip_address'  => $currentIp,
        ]);

        // Check if mandatory first-time password change is required
        if ($user->must_change_password) {
            return redirect()->route('password.force-change');
        }

        // Role-based destination routing
        return match ($user->role) {
            User::ROLE_ADMIN   => redirect()->intended(route('admin.dashboard')),
            User::ROLE_DOCTOR,
            User::ROLE_NURSE,
            User::ROLE_PHARMACIST,
            User::ROLE_LAB_TECH,
            User::ROLE_STAFF   => redirect()->intended(route('staff.dashboard')),
            default            => redirect()->intended(route('patient.dashboard')),
        };
    }

    /**
     * Show the mandatory first-time password change screen.
     */
    public function showForceChangePassword(): View
    {
        return view('auth.force-password-change');
    }

    /**
     * Process mandatory first-time password change.
     */
    public function updateForceChangePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = Auth::user();
        $user->update([
            'password'             => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        AuditLog::create([
            'user_id'     => $user->id,
            'action'      => 'auth.password_first_change',
            'entity_type' => 'User',
            'entity_id'   => $user->id,
            'ip_address'  => $request->ip(),
        ]);

        try {
            Mail::to($user->email)->send(new QueueNotificationMail(
                $user,
                'Security Confirmation: Password Successfully Updated',
                'Your New Password is Active',
                'Your MediQueue account password has been successfully established. Your first-time login security verification is now complete.'
            ));
        } catch (\Throwable $e) {}

        $target = match ($user->role) {
            User::ROLE_ADMIN => route('admin.dashboard'),
            User::ROLE_DOCTOR,
            User::ROLE_NURSE,
            User::ROLE_PHARMACIST,
            User::ROLE_LAB_TECH,
            User::ROLE_STAFF => route('staff.dashboard'),
            default          => route('patient.dashboard'),
        };

        return redirect($target)->with('success', 'Your password has been successfully established and your account is secure.');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            AuditLog::create([
                'user_id'     => Auth::id(),
                'action'      => 'auth.logout',
                'entity_type' => 'User',
                'entity_id'   => Auth::id(),
                'ip_address'  => $request->ip(),
            ]);
            Auth::guard('web')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been securely signed out.');
    }
}
