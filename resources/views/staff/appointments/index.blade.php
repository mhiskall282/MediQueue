<x-layouts.app title="Clinic Appointments Schedule">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Clinic Appointment Schedule</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Daily appointment calendar, patient arrival check-in desk, and automatic queue ticket issuance.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary text-xs">
                    &larr; Back to Clinical Console
                </a>
            </div>
        </div>

        {{-- Metrics & Filters --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="stat-card">
                <span class="text-xs font-bold text-slate-500 uppercase block">Appointments for {{ date('M d', strtotime($selectedDate)) }}</span>
                <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $totalToday }} Bookings</span>
                <span class="text-[11px] text-slate-400">Total scheduled visits</span>
            </div>

            <div class="stat-card">
                <span class="text-xs font-bold text-amber-600 uppercase block">Awaiting Arrival</span>
                <span class="text-2xl font-black text-amber-600 mt-1 block">{{ $pendingCount }} Pending</span>
                <span class="text-[11px] text-slate-400">Ready for check-in upon arrival</span>
            </div>

            <div class="stat-card">
                <span class="text-xs font-bold text-emerald-600 uppercase block">Checked-In to Queue</span>
                <span class="text-2xl font-black text-emerald-600 mt-1 block">{{ $checkedInCount }} Active</span>
                <span class="text-[11px] text-slate-400">Tickets generated in queue</span>
            </div>
        </div>

        {{-- Date Filter Card --}}
        <div class="card p-5 mb-8">
            <form method="GET" action="{{ route('staff.appointments.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="form-label text-xs">Appointment Date</label>
                    <input type="date" name="date" value="{{ $selectedDate }}" class="form-input text-xs">
                </div>

                <div>
                    <label class="form-label text-xs">Department Filter</label>
                    <select name="service_id" class="form-input text-xs">
                        <option value="">All Departments</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ $serviceId == $service->id ? 'selected' : '' }}>
                                {{ $service->name }} ({{ $service->prefix }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="btn btn-primary text-xs w-full">
                        Filter Schedule
                    </button>
                    <a href="{{ route('staff.appointments.index') }}" class="btn btn-secondary text-xs">
                        Today
                    </a>
                </div>
            </form>
        </div>

        {{-- Appointment Schedule Table --}}
        <div class="card overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-600">
                    Scheduled Appointments ({{ $appointments->count() }})
                </h2>
            </div>

            @if($appointments->isEmpty())
                <div class="p-12 text-center text-slate-500">
                    <p class="font-medium text-sm">No appointments scheduled for this date.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Time Slot</th>
                                <th>Patient Name</th>
                                <th>Department</th>
                                <th>Assigned Specialist</th>
                                <th>Symptoms / Notes</th>
                                <th>Status</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $apt)
                                <tr>
                                    <td class="font-mono font-bold text-xs text-indigo-700 bg-indigo-50/50">
                                        {{ $apt->time_slot }}
                                    </td>
                                    <td>
                                        <div class="font-bold text-slate-900">{{ $apt->patient->name }}</div>
                                        <div class="text-xs text-slate-400">{{ $apt->patient->email }} &bull; {{ $apt->patient->phone ?? 'No phone' }}</div>
                                    </td>
                                    <td>
                                        <span class="font-medium text-slate-800">{{ $apt->service->name }}</span>
                                    </td>
                                    <td>
                                        {{ $apt->doctor->name ?? 'Any Specialist' }}
                                    </td>
                                    <td class="text-xs text-slate-500 max-w-xs truncate">
                                        {{ $apt->symptoms_notes ?? '-' }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $apt->status_badge_class }}">
                                            {{ $apt->status }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        @if($apt->status === 'BOOKED')
                                            <form method="POST" action="{{ route('staff.appointments.check-in', $apt) }}" class="inline-block">
                                                @csrf
                                                <button type="submit" class="btn btn-primary btn-sm text-xs font-bold shadow-xs">
                                                    🎟️ Check-In Patient
                                                </button>
                                            </form>
                                        @elseif($apt->status === 'CHECKED_IN' && $apt->queueEntry)
                                            <span class="font-mono font-bold text-xs px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-lg">
                                                Ticket: {{ $apt->queueEntry->queue_number }}
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-400 italic">No action</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
