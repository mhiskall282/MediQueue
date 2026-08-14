<x-layouts.app title="Book Advance Clinic Appointment">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-4">
            <a href="{{ route('patient.appointments.index') }}" class="hover:text-indigo-600">Appointments</a>
            <span>/</span>
            <span class="text-slate-900">Book Appointment</span>
        </div>

        <div class="card p-6 sm:p-8">
            <div class="mb-6">
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Schedule Clinic Appointment</h1>
                <p class="text-xs text-slate-500 mt-1">
                    Select your preferred clinic service department, date, and appointment time slot.
                </p>
            </div>

            <form method="POST" action="{{ route('patient.appointments.store') }}" class="space-y-6">
                @csrf

                {{-- Service Department Selection --}}
                <div>
                    <label class="form-label">Clinical Service / Department *</label>
                    <select name="service_id" class="form-input" required>
                        <option value="">Select a clinic service...</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                {{ $service->name }} (Prefix: {{ $service->prefix }}) &bull; Avg ~{{ $service->avg_duration_minutes }} mins
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Preferred Doctor --}}
                <div>
                    <label class="form-label">Preferred Doctor / Clinician (Optional)</label>
                    <select name="doctor_id" class="form-input">
                        <option value="">Any Available Specialist</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                {{ $doctor->name }} ({{ $doctor->role_label }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Appointment Date --}}
                    <div>
                        <label class="form-label">Appointment Date *</label>
                        <input type="date" name="appointment_date" min="{{ date('Y-m-d') }}" value="{{ old('appointment_date', date('Y-m-d')) }}" class="form-input" required>
                    </div>

                    {{-- Time Slot --}}
                    <div>
                        <label class="form-label">Time Slot *</label>
                        <select name="time_slot" class="form-input" required>
                            <option value="08:30 AM" {{ old('time_slot') == '08:30 AM' ? 'selected' : '' }}>08:30 AM (Morning Slot)</option>
                            <option value="09:00 AM" {{ old('time_slot') == '09:00 AM' ? 'selected' : '' }}>09:00 AM (Morning Slot)</option>
                            <option value="09:30 AM" {{ old('time_slot') == '09:30 AM' ? 'selected' : '' }}>09:30 AM (Morning Slot)</option>
                            <option value="10:00 AM" {{ old('time_slot') == '10:00 AM' ? 'selected' : '' }}>10:00 AM (Morning Slot)</option>
                            <option value="10:30 AM" {{ old('time_slot') == '10:30 AM' ? 'selected' : '' }}>10:30 AM (Morning Slot)</option>
                            <option value="11:00 AM" {{ old('time_slot') == '11:00 AM' ? 'selected' : '' }}>11:00 AM (Morning Slot)</option>
                            <option value="11:30 AM" {{ old('time_slot') == '11:30 AM' ? 'selected' : '' }}>11:30 AM (Morning Slot)</option>
                            <option value="02:00 PM" {{ old('time_slot') == '02:00 PM' ? 'selected' : '' }}>02:00 PM (Afternoon Slot)</option>
                            <option value="02:30 PM" {{ old('time_slot') == '02:30 PM' ? 'selected' : '' }}>02:30 PM (Afternoon Slot)</option>
                            <option value="03:00 PM" {{ old('time_slot') == '03:00 PM' ? 'selected' : '' }}>03:00 PM (Afternoon Slot)</option>
                            <option value="03:30 PM" {{ old('time_slot') == '03:30 PM' ? 'selected' : '' }}>03:30 PM (Afternoon Slot)</option>
                            <option value="04:00 PM" {{ old('time_slot') == '04:00 PM' ? 'selected' : '' }}>04:00 PM (Evening Slot)</option>
                        </select>
                    </div>
                </div>

                {{-- Reason / Symptoms Notes --}}
                <div>
                    <label class="form-label">Chief Complaint / Symptoms (Optional)</label>
                    <textarea name="symptoms_notes" rows="3" class="form-input" placeholder="Briefly describe your symptoms or reason for visit...">{{ old('symptoms_notes') }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('patient.appointments.index') }}" class="btn btn-secondary text-xs">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary text-xs shadow-md">
                        Confirm & Book Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
