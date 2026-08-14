<x-layouts.app title="Edit User - {{ $user->name }}">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-indigo-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to User Accounts
        </a>

        {{-- Edit User Details & Privilege Extension Card --}}
        <div class="card p-8">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Edit User: {{ $user->name }}</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Hospital ID / MRN: <strong class="font-mono text-indigo-700">{{ $user->hospital_id }}</strong></p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="badge {{ $user->role_badge_class }} text-xs font-bold">
                        {{ $user->role_title }}
                    </span>
                    <span class="badge {{ $user->is_active ? 'badge-in-service' : 'badge-cancelled' }}">
                        {{ $user->is_active ? 'Active' : 'Deactivated' }}
                    </span>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="form-label text-xs">Full Name</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            required
                            class="form-input text-xs @error('name') border-rose-400 bg-rose-50 @enderror"
                        >
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="form-label text-xs">Email Address</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            required
                            class="form-input text-xs @error('email') border-rose-400 bg-rose-50 @enderror"
                        >
                        @error('email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="phone" class="form-label text-xs">Phone Number</label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $user->phone) }}"
                            class="form-input text-xs"
                        >
                    </div>

                    <div>
                        <label for="role" class="form-label text-xs">Primary System Role</label>
                        <select id="role" name="role" required class="form-input text-xs font-bold @error('role') border-rose-400 bg-rose-50 @enderror">
                            <option value="doctor" {{ old('role', $user->role) === 'doctor' ? 'selected' : '' }}>🩺 Medical Doctor / Physician</option>
                            <option value="nurse" {{ old('role', $user->role) === 'nurse' ? 'selected' : '' }}>🩹 Staff Nurse / Triage Specialist</option>
                            <option value="pharmacist" {{ old('role', $user->role) === 'pharmacist' ? 'selected' : '' }}>💊 Clinical Pharmacist</option>
                            <option value="lab_tech" {{ old('role', $user->role) === 'lab_tech' ? 'selected' : '' }}>🧪 Laboratory Technologist</option>
                            <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>🏢 Non-Clinical Operations (Front Desk / Reception)</option>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>👑 Hospital Administrator</option>
                            <option value="patient" {{ old('role', $user->role) === 'patient' ? 'selected' : '' }}>👤 Registered Patient</option>
                        </select>
                        @error('role') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="medical_license_number" class="form-label text-xs">Medical License No. (If Applicable)</label>
                        <input
                            type="text"
                            id="medical_license_number"
                            name="medical_license_number"
                            value="{{ old('medical_license_number', $user->medical_license_number) }}"
                            class="form-input text-xs"
                            placeholder="e.g. MMC-748921"
                        >
                    </div>

                    <div>
                        <label for="specialization" class="form-label text-xs">Department / Specialty</label>
                        <input
                            type="text"
                            id="specialization"
                            name="specialization"
                            value="{{ old('specialization', $user->specialization) }}"
                            class="form-input text-xs"
                            placeholder="e.g. General Medicine, Emergency Trauma, Hematology"
                        >
                    </div>
                </div>

                {{-- Dynamic Privilege Extension & Overrides Card --}}
                <div class="p-5 bg-slate-50 border border-slate-200 rounded-2xl space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-indigo-900 flex items-center gap-1.5">
                                <span>🛡️</span> Dynamic Privilege Extension & Overrides
                            </h3>
                            <p class="text-[11px] text-slate-500">Grant custom granular permissions to this user regardless of baseline role.</p>
                        </div>
                        <span class="badge bg-indigo-100 text-indigo-800 text-[10px] font-bold">PoLP ENFORCED</span>
                    </div>

                    @php
                        $userPrivs = $user->extended_privileges ?? [];
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                        <label class="flex items-start gap-2.5 p-3 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-indigo-400 transition-colors">
                            <input type="checkbox" name="extended_privileges[]" value="can_consult" {{ in_array('can_consult', $userPrivs) ? 'checked' : '' }} class="rounded text-indigo-600 mt-0.5">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">🩺 Medical Consultation & Discharge</span>
                                <span class="text-[10px] text-slate-500">Start/complete consultations, write clinical discharge summaries</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-2.5 p-3 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-indigo-400 transition-colors">
                            <input type="checkbox" name="extended_privileges[]" value="can_triage" {{ in_array('can_triage', $userPrivs) ? 'checked' : '' }} class="rounded text-indigo-600 mt-0.5">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">🚨 5-Tier Emergency Triage</span>
                                <span class="text-[10px] text-slate-500">Assess and update Manchester triage acuity levels (P1–P5)</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-2.5 p-3 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-indigo-400 transition-colors">
                            <input type="checkbox" name="extended_privileges[]" value="can_execute_lab" {{ in_array('can_execute_lab', $userPrivs) ? 'checked' : '' }} class="rounded text-indigo-600 mt-0.5">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">🧪 Diagnostic Lab Findings Entry</span>
                                <span class="text-[10px] text-slate-500">Input lab test findings and return patient to doctor review loop</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-2.5 p-3 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-indigo-400 transition-colors">
                            <input type="checkbox" name="extended_privileges[]" value="can_assign_beds" {{ in_array('can_assign_beds', $userPrivs) ? 'checked' : '' }} class="rounded text-indigo-600 mt-0.5">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">🛏️ Hospital Bed & Bay Allocation</span>
                                <span class="text-[10px] text-slate-500">Allocate and release hospital beds across Emergency/Wards</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary text-xs">Cancel</a>
                    <button type="submit" class="btn btn-primary text-xs font-bold">Save User Profile & Privileges</button>
                </div>
            </form>
        </div>

        {{-- Admin Reset Password Card --}}
        <div class="card p-8 border-amber-200 bg-amber-50/20">
            <div class="mb-4">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span>🔑</span> Administrative Password Reset
                </h2>
                <p class="text-xs text-slate-600 mt-0.5">Set a new password for {{ $user->name }}. An email notice will be dispatched automatically upon update.</p>
            </div>

            <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="space-y-4" onsubmit="return confirm('Are you sure you want to reset the password for {{ $user->name }}?');">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="form-label text-xs">New Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            class="form-input text-xs @error('password') border-rose-400 bg-rose-50 @enderror"
                            placeholder="At least 8 characters"
                        >
                        @error('password') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="form-label text-xs">Confirm New Password</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            required
                            class="form-input text-xs"
                            placeholder="Repeat password"
                        >
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn btn-secondary text-xs font-bold text-amber-900 bg-amber-100 hover:bg-amber-200 border-amber-300">
                        Reset Password & Dispatch Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
