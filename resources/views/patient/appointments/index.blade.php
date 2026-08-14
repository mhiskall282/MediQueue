<x-layouts.app title="My Clinic Appointments">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Clinic Appointments</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Book advance consultations with clinical specialists and review scheduled visit dates.
                </p>
            </div>
            <a href="{{ route('patient.appointments.create') }}" class="btn btn-primary text-xs sm:text-sm shadow-md flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Book New Appointment
            </a>
        </div>

        {{-- Upcoming Appointments --}}
        <div class="mb-10">
            <h2 class="text-lg font-black text-slate-900 mb-4 flex items-center gap-2">
                <span>🗓️</span> Upcoming Scheduled Visits ({{ $upcomingAppointments->count() }})
            </h2>

            @if($upcomingAppointments->isEmpty())
                <div class="card p-8 text-center bg-slate-50/50">
                    <div class="w-12 h-12 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-3 text-xl">
                        📅
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm">No upcoming appointments scheduled</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                        Need to see a specialist? Schedule your advance consultation appointment in seconds.
                    </p>
                    <a href="{{ route('patient.appointments.create') }}" class="btn btn-primary btn-sm text-xs mt-4">
                        Book Appointment Now
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($upcomingAppointments as $apt)
                        <div class="card p-6 border-l-4 border-indigo-600 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between gap-4 mb-4">
                                <div>
                                    <span class="text-xs font-bold text-indigo-700 uppercase tracking-wider block">
                                        {{ $apt->service->name }}
                                    </span>
                                    <h3 class="font-black text-slate-900 text-lg mt-0.5">
                                        {{ $apt->appointment_date->format('l, F d, Y') }}
                                    </h3>
                                    <span class="inline-flex items-center gap-1 font-semibold text-slate-700 text-xs mt-1">
                                        ⏰ Slot: {{ $apt->time_slot }}
                                    </span>
                                </div>
                                <span class="badge {{ $apt->status_badge_class }}">
                                    {{ $apt->status }}
                                </span>
                            </div>

                            @if($apt->doctor)
                                <div class="text-xs text-slate-600 mb-3 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                    <span class="font-semibold text-slate-900">Attending Specialist:</span> {{ $apt->doctor->name }}
                                </div>
                            @endif

                            @if($apt->symptoms_notes)
                                <div class="text-xs text-slate-500 mb-3 bg-slate-50/50 p-2.5 rounded-xl border border-slate-100">
                                    <span class="font-semibold text-slate-700">Reason for visit:</span> {{ $apt->symptoms_notes }}
                                </div>
                            @endif

                            @if($apt->doctor_instructions)
                                <div class="text-xs text-indigo-900 mb-4 bg-indigo-50/80 p-3 rounded-xl border border-indigo-200">
                                    <div class="font-bold flex items-center gap-1.5 mb-1 text-indigo-950">
                                        <span>💬</span> Doctor's Pre-Consultation Instructions:
                                    </div>
                                    <p class="text-indigo-800">{{ $apt->doctor_instructions }}</p>
                                </div>
                            @endif

                            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                                <span class="text-[11px] text-slate-400">
                                    Booked on {{ $apt->created_at->format('M d, Y') }}
                                </span>

                                <form method="POST" action="{{ route('patient.appointments.cancel', $apt) }}" onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary btn-sm text-xs text-rose-600 hover:text-rose-800">
                                        Cancel Appointment
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Past Appointments History --}}
        @if($pastAppointments->isNotEmpty())
            <div class="card overflow-hidden">
                <div class="p-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">
                        Appointment History
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Department</th>
                                <th>Date & Time</th>
                                <th>Specialist</th>
                                <th>Status</th>
                                <th>Queue Ticket</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pastAppointments as $apt)
                                <tr>
                                    <td class="font-semibold text-slate-900">{{ $apt->service->name }}</td>
                                    <td class="text-xs text-slate-600">{{ $apt->appointment_date->format('M d, Y') }} at {{ $apt->time_slot }}</td>
                                    <td class="text-xs text-slate-500">{{ $apt->doctor->name ?? 'Any Specialist' }}</td>
                                    <td>
                                        <span class="badge {{ $apt->status_badge_class }}">
                                            {{ $apt->status }}
                                        </span>
                                    </td>
                                    <td class="text-xs font-mono font-bold text-slate-800">
                                        {{ $apt->queueEntry ? $apt->queueEntry->queue_number : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-slate-100">
                    {{ $pastAppointments->links() }}
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
