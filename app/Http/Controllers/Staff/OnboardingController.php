<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Mail\QueueNotificationMail;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    /**
     * Show the staff self-onboarding & credentialing page.
     */
    public function show(): View
    {
        $user = Auth::user();
        return view('staff.onboarding', compact('user'));
    }

    /**
     * Process staff self-onboarding credential submission.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'                    => ['required', 'string', 'max:100'],
            'phone'                   => ['required', 'string', 'max:20'],
            'emergency_contact_phone' => ['required', 'string', 'max:20'],
            'medical_license_number'  => ['required', 'string', 'max:100'],
            'specialization'          => ['required', 'string', 'max:150'],
            'on_call_shift'           => ['nullable', 'string', 'max:50'],
        ]);

        $user->update([
            'name'                    => $validated['name'],
            'phone'                   => $validated['phone'],
            'emergency_contact_phone' => $validated['emergency_contact_phone'],
            'medical_license_number'  => $validated['medical_license_number'],
            'specialization'          => $validated['specialization'],
            'on_call_shift'           => $validated['on_call_shift'] ?? $user->on_call_shift,
        ]);

        AuditLog::create([
            'user_id'     => $user->id,
            'action'      => 'staff.credentials_submitted',
            'entity_type' => 'User',
            'entity_id'   => $user->id,
            'metadata'    => [
                'name'    => $user->name,
                'license' => $user->medical_license_number,
                'dept'    => $user->specialization,
            ],
            'ip_address'  => $request->ip(),
        ]);

        // Notify Hospital Administrators
        $admins = User::where('role', User::ROLE_ADMIN)->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type'    => 'staff.credentialing_submitted',
                'title'   => 'Medical Credential Verification Submitted: '.$user->name,
                'body'    => "Staff member {$user->name} ({$user->role_title}) updated their medical credentials (License: {$user->medical_license_number}) for {$user->specialization}.",
            ]);

            try {
                if ($admin->email_notifications_enabled) {
                    Mail::to($admin->email)->send(new QueueNotificationMail(
                        $admin,
                        'Staff Credential Verification Submitted: '.$user->name,
                        'Medical Staff Credential Verification Awaiting Review',
                        "Staff member {$user->name} ({$user->role_title}) has submitted practicing credentials for administrator verification.",
                        [
                            'Clinician Name'  => $user->name,
                            'Role'            => $user->role_title,
                            'License Number'  => $user->medical_license_number,
                            'Specialization'  => $user->specialization,
                            'Contact Phone'   => $user->phone,
                            'Submission Time' => now()->toDateTimeString(),
                        ]
                    ));
                }
            } catch (\Throwable $e) {}
        }

        return back()->with('success', 'Your clinical credentials have been updated and submitted for Hospital Administrator licensing review.');
    }
}
