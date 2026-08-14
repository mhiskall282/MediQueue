<x-layouts.app title="User Account Management">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">System Users & Roles</h1>
                <p class="text-slate-500 text-sm mt-1">Manage patient, clinical staff, and administrator accounts.</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Staff / Admin Account
            </a>
        </div>

        {{-- Filters --}}
        <div class="card p-4 mb-6">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search by name or email..."
                        class="form-input text-sm"
                    >
                </div>
                <div class="w-full sm:w-48">
                    <select name="role" onchange="this.form.submit()" class="form-input text-sm">
                        <option value="">All Roles</option>
                        <option value="patient" {{ request('role') === 'patient' ? 'selected' : '' }}>Patient</option>
                        <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-secondary text-sm">Filter</button>
            </form>
        </div>

        {{-- Users Table --}}
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Email Address</th>
                            <th>Current Role</th>
                            <th>Account Status</th>
                            <th>Registered</th>
                            <th class="text-right">Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td class="font-bold text-slate-900">{{ $user->name }}</td>
                                <td class="text-slate-600 text-xs">{{ $user->email }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.users.role', $user) }}" class="inline-block">
                                        @csrf
                                        <select name="role" onchange="if(confirm('Change role for this user?')) this.form.submit()" class="text-xs font-semibold py-1 px-2 rounded border border-slate-200 bg-slate-50 text-slate-800">
                                            <option value="patient" {{ $user->role === 'patient' ? 'selected' : '' }}>Patient</option>
                                            <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>Staff</option>
                                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge badge-in-service">Active</span>
                                    @else
                                        <span class="badge badge-cancelled">Deactivated</span>
                                    @endif
                                </td>
                                <td class="text-xs text-slate-500">{{ $user->created_at->format('M d, Y') }}</td>
                                <td class="text-right">
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="inline-block" onsubmit="return confirm('Toggle status for this user?');">
                                            @csrf
                                            <button type="submit" class="btn btn-ghost btn-sm {{ $user->is_active ? 'text-rose-600' : 'text-emerald-600' }}">
                                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Current User</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
