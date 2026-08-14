<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\QueueNotificationMail;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\SecurityAlert;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * List all users with filtering and pending verification tabs.
     */
    public function index(Request $request): View
    {
        $query = User::query();

        if ($request->filled('status') && $request->status === 'pending') {
            $query->where('is_approved', false);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%')
                  ->orWhere('hospital_id', 'like', '%'.$request->search.'%')
                  ->orWhere('medical_license_number', 'like', '%'.$request->search.'%');
            });
        }

        $pendingCount = User::where('is_approved', false)->count();
        $users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users', 'pendingCount'));
    }

    /**
     * Show the create user form.
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Create a new user account with least-privilege role assignment.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'                   => ['required', 'string', 'max:100'],
            'email'                  => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'                  => ['nullable', 'string', 'max:20'],
            'role'                   => ['required', 'in:patient,staff,doctor,nurse,pharmacist,lab_tech,admin'],
            'medical_license_number' => ['nullable', 'string', 'max:100'],
            'specialization'         => ['nullable', 'string', 'max:150'],
            'password'               => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'                   => $data['name'],
            'email'                  => $data['email'],
            'phone'                  => $data['phone'] ?? null,
            'role'                   => $data['role'],
            'specialization'         => $data['specialization'] ?? null,
            'medical_license_number' => $data['medical_license_number'] ?? null,
            'password'               => Hash::make($data['password']),
            'must_change_password'   => true,
            'is_active'              => true,
            'is_approved'            => true,
            'approved_at'            => now(),
            'approved_by'            => Auth::id(),
        ]);

        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'user.created',
            'entity_type' => 'User',
            'entity_id'   => $user->id,
            'metadata'    => ['name' => $user->name, 'role' => $user->role, 'email' => $user->email],
            'ip_address'  => $request->ip(),
        ]);

        try {
            Mail::to($user->email)->send(
                new QueueNotificationMail(
                    $user,
                    'Welcome to MediQueue Clinical Portal',
                    'Your Account is Verified and Ready',
                    "An administrator has provisioned your MediQueue account with the clinical role of {$user->role_title}."
                )
            );
        } catch (\Throwable $e) {}

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$user->name}\" provisioned with role: {$user->role_title}.");
    }

    /**
     * Approve a pending medical staff applicant.
     */
    public function approve(Request $request, User $user): RedirectResponse
    {
        $user->update([
            'is_approved' => true,
            'is_active'   => true,
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'user.staff_approved',
            'entity_type' => 'User',
            'entity_id'   => $user->id,
            'metadata'    => [
                'name'    => $user->name,
                'role'    => $user->role,
                'license' => $user->medical_license_number,
            ],
            'ip_address'  => $request->ip(),
        ]);

        try {
            Mail::to($user->email)->send(
                new QueueNotificationMail(
                    $user,
                    'Medical Staff Access Approved',
                    'Credentials Verified by Hospital Administration',
                    "Your practicing credentials (License: {$user->medical_license_number}) have been verified and approved by the Hospital Administrator. You may now access the Clinical Operations Console with your role: {$user->role_title}."
                )
            );
        } catch (\Throwable $e) {}

        return back()->with('success', "Medical staff account for \"{$user->name}\" ({$user->role_title}) has been verified and activated.");
    }

    /**
     * Revoke access for a clinical staff member or patient.
     */
    public function revoke(Request $request, User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['user' => 'You cannot revoke your own administrator account.']);
        }

        $user->update([
            'is_approved' => false,
            'is_active'   => false,
        ]);

        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'user.access_revoked',
            'entity_type' => 'User',
            'entity_id'   => $user->id,
            'metadata'    => ['name' => $user->name, 'role' => $user->role],
            'ip_address'  => $request->ip(),
        ]);

        SecurityAlert::create([
            'user_id'     => $user->id,
            'event_type'  => 'STAFF_ACCESS_REVOKED',
            'severity'    => SecurityAlert::SEVERITY_MEDIUM,
            'description' => "Clinical privileges and account access revoked for: {$user->name} ({$user->role_title}) by Admin ".Auth::user()->name,
            'ip_address'  => $request->ip(),
        ]);

        return back()->with('success', "Access privileges for \"{$user->name}\" have been revoked.");
    }

    /**
     * Show user edit form.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user details and role.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name'                   => ['required', 'string', 'max:100'],
            'email'                  => ['required', 'email', 'max:255', "unique:users,email,{$user->id}"],
            'phone'                  => ['nullable', 'string', 'max:20'],
            'role'                   => ['required', 'in:patient,staff,doctor,nurse,pharmacist,lab_tech,admin'],
            'specialization'         => ['nullable', 'string', 'max:150'],
            'medical_license_number' => ['nullable', 'string', 'max:100'],
            'extended_privileges'    => ['nullable', 'array'],
            'extended_privileges.*'  => ['string'],
        ]);

        if ($user->id === Auth::id() && $data['role'] !== 'admin') {
            return back()->withErrors(['role' => 'You cannot remove your own admin privileges.']);
        }

        $oldRole = $user->role;
        $data['extended_privileges'] = $request->input('extended_privileges', []);
        $user->update($data);

        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'user.updated',
            'entity_type' => 'User',
            'entity_id'   => $user->id,
            'metadata'    => [
                'name'                => $user->name,
                'from'                => $oldRole,
                'to'                  => $data['role'],
                'extended_privileges' => $data['extended_privileges'],
            ],
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$user->name}\" updated successfully with assigned privileges.");
    }

    /**
     * Reset a user's password.
     */
    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'password'             => Hash::make($data['password']),
            'must_change_password' => true,
        ]);

        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'user.password_reset',
            'entity_type' => 'User',
            'entity_id'   => $user->id,
            'metadata'    => ['name' => $user->name, 'email' => $user->email],
            'ip_address'  => $request->ip(),
        ]);

        Notification::create([
            'user_id' => $user->id,
            'type'    => 'auth.password_reset',
            'title'   => 'Password Reset',
            'body'    => 'Your account password was updated by the clinic administrator.',
        ]);

        try {
            Mail::to($user->email)->send(
                new QueueNotificationMail(
                    $user,
                    'Password Reset Notice',
                    'Your Password Was Reset',
                    'Your MediQueue account password has been updated by the clinic administrator. Please log in using your new credentials.'
                )
            );
        } catch (\Throwable $e) {}

        return redirect()->route('admin.users.index')
            ->with('success', "Password for \"{$user->name}\" has been reset.");
    }

    /**
     * Toggle a user's active status.
     */
    public function toggle(Request $request, User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['user' => 'You cannot deactivate your own account.']);
        }

        $user->update(['is_active' => !$user->is_active]);

        $label = $user->is_active ? 'activated' : 'deactivated';

        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'user.'.($user->is_active ? 'activated' : 'deactivated'),
            'entity_type' => 'User',
            'entity_id'   => $user->id,
            'metadata'    => ['name' => $user->name, 'role' => $user->role],
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$user->name}\" {$label}.");
    }

    /**
     * Update a user's role directly from the table.
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', 'in:staff,doctor,nurse,pharmacist,lab_tech,admin,patient'],
        ]);

        if ($user->id === Auth::id() && $data['role'] !== 'admin') {
            return back()->withErrors(['role' => 'You cannot change your own admin role.']);
        }

        $oldRole = $user->role;
        $user->update(['role' => $data['role']]);

        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'user.role_changed',
            'entity_type' => 'User',
            'entity_id'   => $user->id,
            'metadata'    => ['name' => $user->name, 'from' => $oldRole, 'to' => $data['role']],
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "\"{$user->name}\" role changed from {$oldRole} to {$data['role']}.");
    }
}
