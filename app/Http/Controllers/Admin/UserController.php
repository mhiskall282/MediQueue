<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
     * Show the create staff user form.
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Create a new staff account.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'role'     => ['required', 'in:staff,admin'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
            'role'     => $data['role'],
            'password' => Hash::make($data['password']),
        ]);

        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'user.created',
            'entity_type' => 'User',
            'entity_id'   => $user->id,
            'metadata'    => ['name' => $user->name, 'role' => $user->role],
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$user->name}\" created with role: {$user->role}.");
    }

    /**
     * Toggle a user's active status.
     */
    public function toggle(Request $request, User $user): RedirectResponse
    {
        // Cannot deactivate yourself
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
     * Update a user's role.
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
