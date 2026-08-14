<x-layouts.app title="Staff Credentialing & Onboarding — MediQueue">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
        {{-- Header Status Card --}}
        <div class="card p-6 border-l-4 {{ $user->is_approved ? 'border-emerald-500 bg-emerald-50/20' : 'border-amber-500 bg-amber-50/20' }}">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-black text-slate-900">Medical Staff Credentialing & Profile</h1>
                        <span class="badge {{ $user->is_approved ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }} text-xs font-bold">
                            {{ $user->is_approved ? '✓ Verified Practicing Staff' : '⏳ Licensing Approval Pending' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 mt-1">
                        @if($user->is_approved)
                            Your clinical practicing license has been verified by the Hospital Administrator. Keep your details current below.
                        @else
                            Your account is awaiting final licensing verification from the Hospital Administrator. Complete your professional profile below to expedite review.
                        @endif
                    </p>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-bold text-slate-400 block uppercase">Assigned Hospital ID</span>
                    <span class="font-mono text-xs font-bold text-indigo-700 bg-white px-2 py-1 rounded border border-slate-200">{{ $user->hospital_id }}</span>
                </div>
            </div>
        </div>

        {{-- Professional Credentialing Form --}}
        <div class="card p-8 shadow-sm">
            <div class="mb-6 pb-4 border-b border-slate-100">
                <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <span>🩺</span> Professional Medical Credentials
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Submit your practicing license and department details for UGMC hospital records.</p>
            </div>

            <form method="POST" action="{{ route('staff.onboarding.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="form-label text-xs">Full Legal Name</label>
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
                        <label for="email" class="form-label text-xs">Hospital Email Address</label>
                        <input
                            type="email"
                            id="email"
                            value="{{ $user->email }}"
                            disabled
                            class="form-input text-xs bg-slate-100 text-slate-500 cursor-not-allowed"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="phone" class="form-label text-xs">Primary Contact Phone</label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $user->phone) }}"
                            required
                            class="form-input text-xs @error('phone') border-rose-400 bg-rose-50 @enderror"
                            placeholder="+233 24 123 4567"
                        >
                        @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="emergency_contact_phone" class="form-label text-xs">Emergency Standby Phone</label>
                        <input
                            type="tel"
                            id="emergency_contact_phone"
                            name="emergency_contact_phone"
                            value="{{ old('emergency_contact_phone', $user->emergency_contact_phone) }}"
                            required
                            class="form-input text-xs @error('emergency_contact_phone') border-rose-400 bg-rose-50 @enderror"
                            placeholder="+233 20 987 6543"
                        >
                        @error('emergency_contact_phone') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="medical_license_number" class="form-label text-xs">Medical Practicing License / Pin</label>
                        <input
                            type="text"
                            id="medical_license_number"
                            name="medical_license_number"
                            value="{{ old('medical_license_number', $user->medical_license_number) }}"
                            required
                            class="form-input text-xs font-mono font-bold @error('medical_license_number') border-rose-400 bg-rose-50 @enderror"
                            placeholder="e.g. MDC-GH-89421 or NMC-39218"
                        >
                        @error('medical_license_number') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="specialization" class="form-label text-xs">Department / Clinical Specialty</label>
                        <input
                            type="text"
                            id="specialization"
                            name="specialization"
                            value="{{ old('specialization', $user->specialization) }}"
                            required
                            class="form-input text-xs @error('specialization') border-rose-400 bg-rose-50 @enderror"
                            placeholder="e.g. Emergency Medicine, Hematology, Pediatrics"
                        >
                        @error('specialization') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="on_call_shift" class="form-label text-xs">Preferred On-Call Shift Availability</label>
                    <select id="on_call_shift" name="on_call_shift" class="form-input text-xs">
                        <option value="Morning (08:00 - 16:00)" {{ old('on_call_shift', $user->on_call_shift) === 'Morning (08:00 - 16:00)' ? 'selected' : '' }}>Morning Shift (08:00 - 16:00)</option>
                        <option value="Evening (16:00 - 00:00)" {{ old('on_call_shift', $user->on_call_shift) === 'Evening (16:00 - 00:00)' ? 'selected' : '' }}>Evening Shift (16:00 - 00:00)</option>
                        <option value="Night (00:00 - 08:00)" {{ old('on_call_shift', $user->on_call_shift) === 'Night (00:00 - 08:00)' ? 'selected' : '' }}>Night Standby (00:00 - 08:00)</option>
                        <option value="24/7 STAT Emergency Standby" {{ old('on_call_shift', $user->on_call_shift) === '24/7 STAT Emergency Standby' ? 'selected' : '' }}>24/7 STAT Emergency Standby</option>
                    </select>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('staff.dashboard') }}" class="text-xs text-slate-500 hover:text-indigo-600 font-bold">
                        &larr; Return to Console
                    </a>
                    <button type="submit" class="btn btn-primary text-xs font-bold py-2.5">
                        <span>🛡️</span> Submit & Update Credentials
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
