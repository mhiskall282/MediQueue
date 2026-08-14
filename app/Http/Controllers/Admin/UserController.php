<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\QueueNotificationMail;
use App\Models\AuditLog;
use App\Models\Notification;
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
     * List all users with filtering.
     */
    public function index(Request $request): View
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the create user form.
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Create a new user account (patient, staff, or admin).
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'role'     => ['required', 'in:patient,staff,admin'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'phone'     => $data['phone'] ?? null,
            'role'      => $data['role'],
            'password'  => Hash::make($data['password']),
            'is_active' => true,
        ]);

        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'user.created',
            'entity_type' => 'User',
            'entity_id'   => $user->id,
            'metadata'    => ['name' => $user->name, 'role' => $user->role, 'email' => $user->email],
            'ip_address'  => $request->ip(),
        ]);

        // Send welcome notification & email
        Notification::create([
            'user_id' => $user->id,
            'type'    => 'account.created',
            'title'   => 'Account Created',
            'body'    => "Your MediQueue account has been created with the role of {$user->role_label}.",
        ]);

        try {
            Mail::to($user->email)->send(
                new QueueNotificationMail(
                    $user,
                    'Welcome to MediQueue',
                    'Your Account is Ready',
                    "An administrator has created your MediQueue account with the role of {$user->role_label}. You can now log in using your email address."
                )
            );
        } catch (\Throwable $e) {}

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$user->name}\" created with role: {$user->role_label}.");
    }

    /**
     * Show user edit form.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user details.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', "unique:users,email,{$user->id}"],
            'phone' => ['nullable', 'string', 'max:20'],
            'role'  => ['required', 'in:patient,staff,admin'],
        ]);

        if ($user->id === Auth::id() && $data['role'] !== 'admin') {
            return back()->withErrors(['role' => 'You cannot remove your own admin privileges.']);
        }

        $oldRole = $user->role;
        $user->update($data);

        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'user.updated',
            'entity_type' => 'User',
            'entity_id'   => $user->id,
            'metadata'    => ['name' => $user->name, 'role' => $user->role, 'email' => $user->email],
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$user->name}\" updated successfully.");
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
            'password' => Hash::make($data['password']),
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
            'role' => ['required', 'in:staff,admin,patient'],
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
