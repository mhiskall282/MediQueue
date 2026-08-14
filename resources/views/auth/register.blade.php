<x-layouts.auth title="Create Account">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 mb-1">Create your account</h1>
        <p class="text-slate-500 text-sm mb-6">Join MediQueue to access outpatient queues and clinical desks.</p>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            {{-- Account Type Selection --}}
            <div>
                <label class="form-label text-xs font-bold uppercase tracking-wider text-slate-700">Account Classification</label>
                <div class="grid grid-cols-2 gap-3 mt-1.5">
                    <label class="cursor-pointer border-2 rounded-xl p-3 text-center transition-all flex flex-col items-center gap-1.5" id="patientCard" onclick="selectRole('patient')">
                        <input type="radio" name="role" value="patient" checked class="sr-only" id="rolePatient">
                        <span class="text-xl">👤</span>
                        <span class="text-xs font-bold text-slate-900">Patient</span>
                        <span class="text-[10px] text-slate-500">Virtual queueing & appointments</span>
                    </label>

                    <label class="cursor-pointer border-2 rounded-xl p-3 text-center transition-all flex flex-col items-center gap-1.5" id="staffCard" onclick="selectRole('doctor')">
                        <input type="radio" name="role" value="doctor" class="sr-only" id="roleStaff">
                        <span class="text-xl">🩺</span>
                        <span class="text-xs font-bold text-slate-900">Medical Staff</span>
                        <span class="text-[10px] text-slate-500">Requires Admin vetting</span>
                    </label>
                </div>
            </div>

            {{-- Staff Specific Role & License Fields (Shown when Medical Staff is chosen) --}}
            <div id="staffFields" class="hidden p-4 bg-indigo-50/70 border border-indigo-200 rounded-2xl space-y-3">
                <div class="flex items-center gap-2 text-xs font-bold text-indigo-900">
                    <span>🛡️</span> Medical Credentials & Least Privilege
                </div>

                <div>
                    <label for="staffRoleSelect" class="form-label text-xs">Medical Designation</label>
                    <select id="staffRoleSelect" onchange="updateStaffRole(this.value)" class="form-input text-xs">
                        <option value="doctor">Medical Doctor / Physician</option>
                        <option value="nurse">Staff Nurse / Triage Specialist</option>
                        <option value="pharmacist">Clinical Pharmacist</option>
                        <option value="lab_tech">Laboratory Technologist</option>
                        <option value="staff">Clinical Operations Staff</option>
                    </select>
                </div>

                <div>
                    <label for="medical_license_number" class="form-label text-xs">Practicing Medical License No.</label>
                    <input
                        type="text"
                        id="medical_license_number"
                        name="medical_license_number"
                        value="{{ old('medical_license_number') }}"
                        class="form-input text-xs"
                        placeholder="e.g. MMC-748921 or RN-99238"
                    >
                </div>

                <div>
                    <label for="specialization" class="form-label text-xs">Clinical Specialty / Department</label>
                    <input
                        type="text"
                        id="specialization"
                        name="specialization"
                        value="{{ old('specialization') }}"
                        class="form-input text-xs"
                        placeholder="e.g. Emergency Medicine, Hematology, General Outpatient"
                    >
                </div>

                <p class="text-[11px] text-indigo-700 leading-tight">
                    * In compliance with ISO 27001 & HIPAA policies, staff accounts are vetted by hospital administration before access to clinical records is authorized.
                </p>
            </div>

            {{-- Name --}}
            <div>
                <label for="name" class="form-label">Full name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autocomplete="name"
                    class="form-input @error('name') border-rose-400 bg-rose-50 @enderror"
                    placeholder="Your full name"
                >
                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="form-label">Email address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    class="form-input @error('email') border-rose-400 bg-rose-50 @enderror"
                    placeholder="you@example.com"
                >
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Phone --}}
            <div>
                <label for="phone" class="form-label">
                    Phone number <span class="text-slate-400 font-normal">(optional)</span>
                </label>
                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    value="{{ old('phone') }}"
                    class="form-input"
                    placeholder="+60 12 345 6789"
                >
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    class="form-input @error('password') border-rose-400 bg-rose-50 @enderror"
                    placeholder="At least 8 characters"
                >
                @error('password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password Confirm --}}
            <div>
                <label for="password_confirmation" class="form-label">Confirm password</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="form-input"
                    placeholder="Repeat your password"
                >
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn btn-primary w-full justify-center py-2.5 mt-2">
                Create Account
            </button>
        </form>

        <p class="text-center text-xs text-slate-500 mt-6">
            Already have an account?
            <a href="{{ route('login') }}" class="text-indigo-600 font-semibold hover:underline">
                Sign in
            </a>
        </p>
    </div>

    <script>
        function selectRole(role) {
            const patientCard = document.getElementById('patientCard');
            const staffCard = document.getElementById('staffCard');
            const staffFields = document.getElementById('staffFields');
            const rolePatient = document.getElementById('rolePatient');
            const roleStaff = document.getElementById('roleStaff');

            if (role === 'patient') {
                rolePatient.checked = true;
                patientCard.classList.add('border-indigo-600', 'bg-indigo-50/50');
                patientCard.classList.remove('border-slate-200');
                staffCard.classList.remove('border-indigo-600', 'bg-indigo-50/50');
                staffCard.classList.add('border-slate-200');
                staffFields.classList.add('hidden');
                document.getElementById('medical_license_number').removeAttribute('required');
            } else {
                roleStaff.checked = true;
                staffCard.classList.add('border-indigo-600', 'bg-indigo-50/50');
                staffCard.classList.remove('border-slate-200');
                patientCard.classList.remove('border-indigo-600', 'bg-indigo-50/50');
                patientCard.classList.add('border-slate-200');
                staffFields.classList.remove('hidden');
                document.getElementById('medical_license_number').setAttribute('required', 'required');
            }
        }

        function updateStaffRole(val) {
            document.getElementById('roleStaff').value = val;
        }

        // Initialize state
        document.addEventListener('DOMContentLoaded', () => {
            selectRole('patient');
        });
    </script>
</x-layouts.auth>
