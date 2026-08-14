<x-layouts.app title="Edit User - {{ $user->name }}">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-indigo-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Users
        </a>

        {{-- Edit User Details Card --}}
        <div class="card p-8">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Edit User: {{ $user->name }}</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Modify account information and permissions.</p>
                </div>
                <span class="badge {{ $user->is_active ? 'badge-in-service' : 'badge-cancelled' }}">
                    {{ $user->is_active ? 'Active' : 'Deactivated' }}
                </span>
            </div>

            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="form-label">Full Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        required
                        class="form-input @error('name') border-rose-400 bg-rose-50 @enderror"
                    >
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="form-label">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        class="form-input @error('email') border-rose-400 bg-rose-50 @enderror"
                    >
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="form-label">Phone Number (Optional)</label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        value="{{ old('phone', $user->phone) }}"
                        class="form-input"
                    >
                </div>

                <div>
                    <label for="role" class="form-label">System Role & Access Level</label>
                    <select id="role" name="role" required class="form-input @error('role') border-rose-400 bg-rose-50 @enderror">
                        <option value="patient" {{ old('role', $user->role) === 'patient' ? 'selected' : '' }}>Patient (Queue Self-Service)</option>
                        <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Clinical Staff (Doctor / Nurse / Operator)</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>System Administrator (Full Control)</option>
                    </select>
                    @error('role') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>

        {{-- Admin Reset Password Card --}}
        <div class="card p-8 border-amber-200 bg-amber-50/20">
            <div class="mb-6">
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Administrative Password Reset
                </h2>
                <p class="text-xs text-slate-600 mt-1">Set a new password for {{ $user->name }}. An email notice will be dispatched automatically upon update.</p>
            </div>

            <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="space-y-4" onsubmit="return confirm('Are you sure you want to reset the password for {{ $user->name }}?');">
                @csrf

                <div>
                    <label for="password" class="form-label">New Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="form-input @error('password') border-rose-400 bg-rose-50 @enderror"
                        placeholder="••••••••"
                    >
                    @error('password') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        class="form-input"
                        placeholder="••••••••"
                    >
                </div>

                <div class="pt-3">
                    <button type="submit" class="btn btn-warning shadow-sm">
                        Reset User Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
