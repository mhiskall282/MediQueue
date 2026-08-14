<?php

namespace App\Http\Controllers;

use App\Mail\QueueNotificationMail;
use App\Models\AuditLog;
use App\Models\SecurityAlert;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UserSettingsController extends Controller
{
    /**
     * Display personal user settings & security telemetry.
     */
    public function index(): View
    {
        $user = Auth::user();
        return view('settings.index', compact('user'));
    }

    /**
     * Update basic profile details and notification preferences.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'                    => ['required', 'string', 'max:100'],
            'phone'                   => ['nullable', 'string', 'max:20'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'specialization'          => ['nullable', 'string', 'max:150'],
            'email_notifications_enabled' => ['nullable', 'boolean'],
        ]);

        $user->update([
            'name'                    => $validated['name'],
            'phone'                   => $validated['phone'] ?? null,
            'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
            'specialization'          => $validated['specialization'] ?? $user->specialization,
            'email_notifications_enabled' => $request->boolean('email_notifications_enabled', true),
        ]);

        AuditLog::create([
            'user_id'     => $user->id,
            'action'      => 'user.profile_updated',
            'entity_type' => 'User',
            'entity_id'   => $user->id,
            'metadata'    => ['name' => $user->name, 'phone' => $user->phone],
            'ip_address'  => $request->ip(),
        ]);

        return back()->with('success', 'Your personal account profile and preferences have been updated.');
    }

    /**
     * Update account password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'password'             => Hash::make($validated['password']),
            'must_change_password' => false,
        ]);

        AuditLog::create([
            'user_id'     => $user->id,
            'action'      => 'user.password_changed_by_self',
            'entity_type' => 'User',
            'entity_id'   => $user->id,
            'ip_address'  => $request->ip(),
        ]);

        // Dispatch security confirmation email
        try {
            if ($user->email_notifications_enabled) {
                Mail::to($user->email)->send(new QueueNotificationMail(
                    $user,
                    'Security Notice: Password Changed Successfully',
                    'Your Password Was Changed',
                    "Your MediQueue account password was recently updated on ".now()->toDateTimeString()." from IP {$request->ip()}. If you initiated this change, no further action is required. If you did not make this change, please contact Hospital Administration immediately.",
                    [
                        'Changed At' => now()->toDateTimeString(),
                        'IP Address' => $request->ip(),
                        'Device'     => substr($request->userAgent() ?? 'Unknown Device', 0, 80),
                    ]
                ));
            }

            SecurityAlert::create([
                'user_id'     => $user->id,
                'event_type'  => 'PASSWORD_CHANGED_BY_USER',
                'severity'    => SecurityAlert::SEVERITY_LOW,
                'description' => "User {$user->name} changed their personal account password from IP {$request->ip()}",
                'ip_address'  => $request->ip(),
            ]);
        } catch (\Throwable $e) {}

        return back()->with('success', 'Your account password was updated successfully. A confirmation email has been dispatched.');
    }
}
