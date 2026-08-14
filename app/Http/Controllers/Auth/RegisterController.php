<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SecurityAlert;
use App\Mail\QueueNotificationMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Show the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $role = $request->input('role', User::ROLE_PATIENT);
        $isStaffRole = in_array($role, [
            User::ROLE_DOCTOR,
            User::ROLE_NURSE,
            User::ROLE_PHARMACIST,
            User::ROLE_LAB_TECH,
            User::ROLE_STAFF,
        ], true);

        $rules = [
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone'    => ['nullable', 'string', 'max:20'],
            'role'     => ['nullable', 'string', 'in:patient,doctor,nurse,pharmacist,lab_tech,staff'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        if ($isStaffRole) {
            $rules['medical_license_number'] = ['required', 'string', 'max:100'];
            $rules['specialization']         = ['nullable', 'string', 'max:150'];
        }

        $validated = $request->validate($rules);

        $user = User::create([
            'name'                   => $validated['name'],
            'email'                  => $validated['email'],
            'phone'                  => $validated['phone'] ?? null,
            'password'               => Hash::make($validated['password']),
            'role'                   => $role,
            'specialization'         => $validated['specialization'] ?? null,
            'medical_license_number' => $validated['medical_license_number'] ?? null,
            'is_approved'            => !$isStaffRole, // Patients auto-approved; Staff requires admin vetting
            'is_active'              => true,
        ]);

        event(new Registered($user));

        if ($isStaffRole) {
            // Notify System Administrators of pending staff verification
            try {
                $admins = User::where('role', User::ROLE_ADMIN)->get();
                foreach ($admins as $admin) {
                    Mail::to($admin->email)->send(new QueueNotificationMail(
                        $admin,
                        "Pending Medical Staff Verification: {$user->name} ({$user->role_title})",
                        'Staff Credential Verification Required',
                        "New medical staff registration received from {$user->name} ({$user->role_title}). Medical License No: {$user->medical_license_number}. Please review and approve access in the Administrator Portal.",
                        [
                            'Clinician Name' => $user->name,
                            'Requested Role' => $user->role_title,
                            'License No.'    => $user->medical_license_number,
                            'Email'          => $user->email,
                            'Submitted At'   => now()->toDateTimeString(),
                        ]
                    ));
                }

                SecurityAlert::create([
                    'user_id'     => $user->id,
                    'event_type'  => 'STAFF_REGISTRATION_SUBMITTED',
                    'severity'    => SecurityAlert::SEVERITY_LOW,
                    'description' => "New medical personnel account submitted awaiting verification: {$user->name} ({$user->role_title})",
                    'ip_address'  => $request->ip(),
                ]);
            } catch (\Throwable $e) {
                // Ignore mail failure
            }

            return redirect()->route('login')
                ->with('success', 'Medical staff registration submitted! To comply with hospital governance and HIPAA/ISO 27001 policies, your account is queued for Administrator verification. You will be notified via email once approved.');
        }

        // Standard Patient Registration
        Auth::login($user);

        return redirect()->route('patient.dashboard')
            ->with('success', 'Welcome to MediQueue! Your patient account and Medical Record Number (MRN) have been created.');
    }
}
