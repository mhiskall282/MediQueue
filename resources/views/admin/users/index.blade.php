<x-layouts.app title="User Account & Medical License Management">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">System Users & Roles &mdash; Clinical Personnel</h1>
                    @if($pendingCount > 0)
                        <span class="badge bg-amber-100 text-amber-800 font-bold text-xs animate-pulse">
                            ⚠️ {{ $pendingCount }} Pending Verification
                        </span>
                    @endif
                </div>
                <p class="text-slate-500 text-xs mt-1">Vetting medical licenses, least-privilege role assignments, and HIPAA / ISO 27001 access control.</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm text-xs font-bold flex items-center gap-1.5 shadow-sm">
                <span>➕</span> Provision Staff Account
            </a>
        </div>

        {{-- Verification Status Tabs --}}
        <div class="flex border-b border-slate-200 gap-6 text-xs font-bold">
            <a href="{{ route('admin.users.index') }}" class="pb-3 border-b-2 {{ !request('status') ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-900' }}">
                All Active Accounts ({{ $users->total() }})
            </a>
            <a href="{{ route('admin.users.index', ['status' => 'pending']) }}" class="pb-3 border-b-2 flex items-center gap-2 {{ request('status') === 'pending' ? 'border-amber-600 text-amber-700' : 'border-transparent text-slate-500 hover:text-slate-900' }}">
                <span>🛡️ Pending Staff Approvals</span>
                @if($pendingCount > 0)
                    <span class="bg-amber-600 text-white rounded-full px-2 py-0.2 text-[10px]">{{ $pendingCount }}</span>
                @endif
            </a>
        </div>

        {{-- Filters --}}
        <div class="card p-4">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="sm:col-span-2">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search by name, email, MRN or Medical License..."
                        class="form-input text-xs"
                    >
                </div>
                <div>
                    <select name="role" onchange="this.form.submit()" class="form-input text-xs">
                        <option value="">All Roles</option>
                        <option value="doctor" {{ request('role') === 'doctor' ? 'selected' : '' }}>🩺 Medical Doctor</option>
                        <option value="nurse" {{ request('role') === 'nurse' ? 'selected' : '' }}>🩹 Staff Nurse</option>
                        <option value="pharmacist" {{ request('role') === 'pharmacist' ? 'selected' : '' }}>💊 Pharmacist</option>
                        <option value="lab_tech" {{ request('role') === 'lab_tech' ? 'selected' : '' }}>🧪 Lab Tech</option>
                        <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>🏢 Operations Staff</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>👑 Administrator</option>
                        <option value="patient" {{ request('role') === 'patient' ? 'selected' : '' }}>👤 Patient</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-secondary text-xs font-bold w-full justify-center">Filter</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-ghost text-xs">Reset</a>
                </div>
            </form>
        </div>

        {{-- Users Table --}}
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Clinician / User</th>
                            <th>Hospital ID / MRN</th>
                            <th>Medical License</th>
                            <th>Clinical Role</th>
                            <th>Approval & Access</th>
                            <th>Registered</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr class="{{ !$user->is_approved ? 'bg-amber-50/40' : '' }}">
                                <td>
                                    <div class="font-bold text-slate-900 text-xs">{{ $user->name }}</div>
                                    <div class="text-slate-500 text-[11px]">{{ $user->email }}</div>
                                </td>
                                <td>
                                    <span class="font-mono text-xs font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">
                                        {{ $user->hospital_id ?? '—' }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->medical_license_number)
                                        <span class="font-mono text-xs text-slate-800 font-semibold">
                                            {{ $user->medical_license_number }}
                                        </span>
                                        @if($user->specialization)
                                            <span class="text-[10px] text-slate-400 block">{{ $user->specialization }}</span>
                                        @endif
                                    @else
                                        <span class="text-xs text-slate-400">N/A (Patient)</span>
                                    @endif
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.users.role', $user) }}" class="inline-block">
                                        @csrf
                                        <select name="role" onchange="if(confirm('Update clinical role for {{ $user->name }}?')) this.form.submit()" class="text-[11px] font-bold py-1 px-2 rounded border border-slate-200 bg-slate-50 text-slate-800">
                                            <option value="doctor" {{ $user->role === 'doctor' ? 'selected' : '' }}>🩺 Doctor</option>
                                            <option value="nurse" {{ $user->role === 'nurse' ? 'selected' : '' }}>🩹 Nurse</option>
                                            <option value="pharmacist" {{ $user->role === 'pharmacist' ? 'selected' : '' }}>💊 Pharmacist</option>
                                            <option value="lab_tech" {{ $user->role === 'lab_tech' ? 'selected' : '' }}>🧪 Lab Tech</option>
                                            <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>🏢 Staff</option>
                                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>👑 Admin</option>
                                            <option value="patient" {{ $user->role === 'patient' ? 'selected' : '' }}>👤 Patient</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    @if(!$user->is_approved)
                                        <span class="badge bg-amber-100 text-amber-800 text-[10px] font-bold animate-pulse">
                                            ⚠️ Pending Vetting
                                        </span>
                                    @elseif($user->is_active)
                                        <span class="badge bg-emerald-100 text-emerald-800 text-[10px] font-semibold">
                                            ✓ Active & Approved
                                        </span>
                                    @else
                                        <span class="badge bg-rose-100 text-rose-800 text-[10px] font-semibold">
                                            ✕ Revoked
                                        </span>
                                    @endif
                                </td>
                                <td class="text-xs text-slate-500">{{ $user->created_at->format('M d, Y') }}</td>
                                <td class="text-right space-x-1">
                                    {{-- Approval & Revocation Controls --}}
                                    @if(!$user->is_approved)
                                        <form method="POST" action="{{ route('admin.users.approve', $user) }}" class="inline-block">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-xs text-[10px] font-bold bg-emerald-600 hover:bg-emerald-500">
                                                ✓ Verify & Approve
                                            </button>
                                        </form>
                                    @elseif($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.revoke', $user) }}" class="inline-block" onsubmit="return confirm('Revoke clinical privileges for this user?');">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary btn-xs text-[10px] text-rose-600 hover:bg-rose-50 font-bold">
                                                Revoke
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-secondary btn-xs text-[10px]">
                                        Edit
                                    </a>
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
