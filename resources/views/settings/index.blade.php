<x-layouts.app title="My Account Settings & Security">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Account Profile & Security Settings</h1>
                <p class="text-xs text-slate-500 mt-1">Manage your personal credentials, contact info, security notifications, and session history.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="badge {{ $user->role_badge_class }} text-xs font-bold">
                    {{ $user->role_title }}
                </span>
                <span class="font-mono text-xs font-bold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-200">
                    {{ $user->hospital_id }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Left Column: Profile Details & Preferences (7 cols) --}}
            <div class="lg:col-span-7 space-y-6">
                {{-- Profile Info Card --}}
                <div class="card p-6 shadow-sm">
                    <h2 class="text-sm font-black text-slate-900 mb-1 flex items-center gap-2">
                        <span>👤</span> Personal Information
                    </h2>
                    <p class="text-xs text-slate-500 mb-4">Your verified clinic profile data.</p>

                    <form method="POST" action="{{ route('settings.profile') }}" class="space-y-4">
                        @csrf
                        @method('PUT')

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
                                value="{{ $user->email }}"
                                disabled
                                class="form-input text-xs bg-slate-100 text-slate-500 cursor-not-allowed"
                            >
                            <span class="text-[10px] text-slate-400">Email changes require hospital administrator verification.</span>
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
                                    placeholder="+233 24 123 4567"
                                >
                            </div>

                            <div>
                                <label for="emergency_contact_phone" class="form-label text-xs">Emergency Contact Phone</label>
                                <input
                                    type="tel"
                                    id="emergency_contact_phone"
                                    name="emergency_contact_phone"
                                    value="{{ old('emergency_contact_phone', $user->emergency_contact_phone) }}"
                                    class="form-input text-xs"
                                    placeholder="+233 20 987 6543"
                                >
                            </div>
                        </div>

                        @if($user->isStaff())
                            <div>
                                <label for="specialization" class="form-label text-xs">Department / Clinical Specialty</label>
                                <input
                                    type="text"
                                    id="specialization"
                                    name="specialization"
                                    value="{{ old('specialization', $user->specialization) }}"
                                    class="form-input text-xs"
                                >
                            </div>

                            @if($user->medical_license_number)
                                <div>
                                    <label class="form-label text-xs">Practicing Medical License No.</label>
                                    <input
                                        type="text"
                                        value="{{ $user->medical_license_number }}"
                                        disabled
                                        class="form-input text-xs bg-slate-100 text-slate-500 font-mono"
                                    >
                                </div>
                            @endif
                        @endif

                        {{-- Email Notification Toggle --}}
                        <div class="pt-2">
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox" name="email_notifications_enabled" value="1" {{ $user->email_notifications_enabled ? 'checked' : '' }} class="rounded text-indigo-600">
                                <div>
                                    <span class="text-xs font-bold text-slate-900 block">Transactional Email Notifications</span>
                                    <span class="text-[11px] text-slate-500">Receive queue call alerts, appointment confirmations, and sign-in notices.</span>
                                </div>
                            </label>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex justify-end">
                            <button type="submit" class="btn btn-primary text-xs font-bold">
                                Save Profile Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Right Column: Password & Login Telemetry (5 cols) --}}
            <div class="lg:col-span-5 space-y-6">
                {{-- Update Password Card --}}
                <div class="card p-6 shadow-sm">
                    <h2 class="text-sm font-black text-slate-900 mb-1 flex items-center gap-2">
                        <span>🔒</span> Update Password
                    </h2>
                    <p class="text-xs text-slate-500 mb-4">Ensure your account uses a strong, private password.</p>

                    <form method="POST" action="{{ route('settings.password') }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="current_password" class="form-label text-xs">Current Password</label>
                            <input
                                type="password"
                                id="current_password"
                                name="current_password"
                                required
                                class="form-input text-xs @error('current_password') border-rose-400 bg-rose-50 @enderror"
                            >
                            @error('current_password') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

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
                            >
                        </div>

                        <div class="pt-2 border-t border-slate-100">
                            <button type="submit" class="btn btn-secondary text-xs font-bold w-full justify-center">
                                Change Password
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Security & Sign-in Telemetry Card --}}
                <div class="card p-5 bg-slate-50/80 border border-slate-200">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3 flex items-center gap-2">
                        <span>🛡️</span> Sign-In Security Telemetry
                    </h3>

                    <div class="space-y-2.5 text-xs">
                        <div class="flex justify-between items-center py-1.5 border-b border-slate-200/60">
                            <span class="text-slate-500">Last Sign-in IP:</span>
                            <span class="font-mono font-bold text-slate-800">{{ $user->last_login_ip ?? request()->ip() }}</span>
                        </div>

                        <div class="flex justify-between items-center py-1.5 border-b border-slate-200/60">
                            <span class="text-slate-500">Last Sign-in Time:</span>
                            <span class="font-medium text-slate-800">{{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : now()->format('M d, Y H:i') }}</span>
                        </div>

                        <div class="flex justify-between items-center py-1.5 border-b border-slate-200/60">
                            <span class="text-slate-500">Hospital Entity:</span>
                            <span class="font-bold text-indigo-700">UGMC (University of Ghana Medical Centre)</span>
                        </div>

                        <div class="flex justify-between items-center py-1.5">
                            <span class="text-slate-500">Audit Status:</span>
                            <span class="badge bg-emerald-100 text-emerald-800 text-[10px]">✓ ISO 27001 Verified</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
