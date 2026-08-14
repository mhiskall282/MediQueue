<x-layouts.app title="Staff Clinical Console — MediQueue">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Top Bar & Service Selector --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Clinical Operations Console</h1>
                <p class="text-sm text-slate-500 mt-1">Manage patient triage, queue callouts, consultations, and bed allocations.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('staff.beds.index') }}" class="btn btn-secondary text-xs sm:text-sm font-bold flex items-center gap-1.5 border-indigo-200 text-indigo-700 bg-indigo-50/50 hover:bg-indigo-100">
                    <span>🛏️</span> Ward & Bed Manager
                </a>
                <a href="{{ route('staff.appointments.index') }}" class="btn btn-secondary text-xs sm:text-sm font-bold flex items-center gap-1.5 border-blue-200 text-blue-700 bg-blue-50/50 hover:bg-blue-100">
                    <span>📅</span> Appointments Desk
                </a>

                {{-- Service Switcher --}}
                <form method="GET" action="{{ route('staff.dashboard') }}" class="flex items-center gap-2">
                    <select name="service_id" onchange="this.form.submit()" class="form-input text-xs py-2">
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ $selectedService && $selectedService->id === $service->id ? 'selected' : '' }}>
                                {{ $service->name }} ({{ $service->prefix }})
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        @if(!$selectedService)
            <div class="card p-12 text-center text-slate-500">
                <p class="font-medium text-sm">No active clinic services configured. Please contact the administrator.</p>
            </div>
        @else
            {{-- Metrics Row --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="stat-card">
                    <span class="text-xs font-bold text-slate-500 uppercase block">Waiting In Line</span>
                    <span class="text-3xl font-black text-slate-900 mt-1 block">{{ $waitingEntries->count() }}</span>
                    <span class="text-[11px] text-slate-400">Patients in {{ $selectedService->prefix }} queue</span>
                </div>

                <div class="stat-card">
                    <span class="text-xs font-bold text-indigo-600 uppercase block">Currently Called</span>
                    <span class="text-3xl font-black text-indigo-600 mt-1 block">{{ $calledEntries->count() }}</span>
                    <span class="text-[11px] text-slate-400">Active consultations / callouts</span>
                </div>

                <div class="stat-card">
                    <span class="text-xs font-bold text-emerald-600 uppercase block">Completed Today</span>
                    <span class="text-3xl font-black text-emerald-600 mt-1 block">{{ $completedToday }}</span>
                    <span class="text-[11px] text-slate-400">Finished consultations</span>
                </div>

                <div class="stat-card">
                    <span class="text-xs font-bold text-slate-500 uppercase block">Avg Service Time</span>
                    <span class="text-3xl font-black text-slate-900 mt-1 block">~{{ $selectedService->avg_duration_minutes }}m</span>
                    <span class="text-[11px] text-slate-400">Target duration per patient</span>
                </div>
            </div>

            {{-- Main Operational Workspace --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Left: Call Next & Active Patient Card --}}
                <div class="space-y-6">
                    {{-- Call Next Action Box --}}
                    <div class="card p-6 bg-gradient-to-br from-indigo-900 to-slate-900 text-white shadow-xl">
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-300 block mb-2">Queue Dispatcher</span>
                        <h2 class="text-xl font-black text-white mb-2">Next Patient in Line</h2>
                        <p class="text-xs text-indigo-200 mb-6">Calls the next waiting patient according to clinical triage severity and sequence.</p>

                        <form method="POST" action="{{ route('staff.queue.call-next') }}">
                            @csrf
                            <input type="hidden" name="service_id" value="{{ $selectedService->id }}">
                            <button type="submit" {{ $waitingEntries->isEmpty() ? 'disabled' : '' }} class="btn bg-indigo-500 hover:bg-indigo-400 text-white font-black py-4 w-full shadow-lg text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                📢 CALL NEXT PATIENT
                            </button>
                        </form>
                    </div>

                    {{-- Active / Called Patients Container --}}
                    <div class="card p-6">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4">
                            Active Consultations ({{ $calledEntries->count() }})
                        </h3>

                        @if($calledEntries->isEmpty())
                            <div class="text-center py-8 text-slate-400 text-xs">
                                No patient currently called. Click <strong>Call Next Patient</strong> to begin consultation.
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($calledEntries as $entry)
                                    <div class="border border-indigo-100 bg-indigo-50/40 rounded-2xl p-4 shadow-xs">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-2xl font-black text-indigo-700 font-mono">{{ $entry->queue_number }}</span>
                                            <span class="badge {{ $entry->status_badge_class }}">{{ $entry->status_label }}</span>
                                        </div>

                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-xs font-bold text-slate-900">{{ $entry->patient->name }}</span>
                                            <span class="badge text-[10px] px-2 py-0.5 {{ $entry->triage_badge_class }}">
                                                {{ $entry->triage_level }}
                                            </span>
                                        </div>

                                        @if($entry->allocatedBed)
                                            <div class="p-2 bg-white rounded-xl border border-indigo-200 text-xs mb-3 flex items-center justify-between">
                                                <span class="font-bold text-indigo-900">🛏️ Bed {{ $entry->allocatedBed->bed_number }} ({{ $entry->allocatedBed->ward_name }})</span>
                                                <form method="POST" action="{{ route('staff.queue.release-bed', $entry) }}">
                                                    @csrf
                                                    <button type="submit" class="text-[10px] text-rose-600 font-bold hover:underline">Release</button>
                                                </form>
                                            </div>
                                        @endif

                                        <p class="text-[11px] text-slate-500 mb-3">Called at: {{ $entry->called_at?->format('g:i A') }}</p>

                                        {{-- Actions --}}
                                        <div class="pt-3 border-t border-indigo-100 flex flex-wrap gap-2">
                                            @if($entry->status === 'CALLED')
                                                <form method="POST" action="{{ route('staff.queue.start', $entry) }}" class="flex-1">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm w-full justify-center text-xs">
                                                        Start Consultation
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('staff.queue.skip', $entry) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-ghost btn-sm text-amber-700 hover:bg-amber-100 text-xs">
                                                        Skip
                                                    </button>
                                                </form>
                                            @elseif($entry->status === 'IN_SERVICE')
                                                <form method="POST" action="{{ route('staff.queue.complete', $entry) }}" class="w-full">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary btn-sm w-full justify-center text-xs font-bold">
                                                        Complete Consultation
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Right: Waiting Patients Queue with Triage Assessment & Bed Allocation --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="card overflow-hidden">
                        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-black text-slate-900">Waiting Patients Queue (Triage Prioritized)</h2>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $waitingEntries->count() }} patients waiting in line for {{ $selectedService->name }}</p>
                            </div>
                        </div>

                        @if($waitingEntries->isEmpty())
                            <div class="p-12 text-center text-slate-400 text-sm">
                                <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                No patients are waiting in the queue at this time.
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Pos</th>
                                            <th>Ticket #</th>
                                            <th>Patient Name</th>
                                            <th>Triage Level</th>
                                            <th>Bed / Bay</th>
                                            <th>Joined</th>
                                            <th>Wait Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($waitingEntries as $idx => $entry)
                                            <tr class="{{ $entry->triage_level === 'RED' ? 'bg-red-50/50' : ($entry->triage_level === 'ORANGE' ? 'bg-orange-50/50' : '') }}">
                                                <td class="font-bold text-slate-500">#{{ $idx + 1 }}</td>
                                                <td class="font-black text-indigo-700 font-mono">{{ $entry->queue_number }}</td>
                                                <td>
                                                    <div class="font-semibold text-slate-900">{{ $entry->patient->name }}</div>
                                                    <div class="text-[11px] text-slate-400">ID: #{{ $entry->patient_id }}</div>
                                                </td>
                                                <td>
                                                    {{-- Quick Triage Form --}}
                                                    <form method="POST" action="{{ route('staff.queue.triage', $entry) }}" class="inline-block">
                                                        @csrf
                                                        <select name="triage_level" onchange="this.form.submit()" class="text-[11px] font-bold rounded-lg border-0 py-1 px-2 cursor-pointer shadow-xs {{ $entry->triage_badge_class }}">
                                                            <option value="RED" {{ $entry->triage_level === 'RED' ? 'selected' : '' }}>🔴 Red (P1)</option>
                                                            <option value="ORANGE" {{ $entry->triage_level === 'ORANGE' ? 'selected' : '' }}>🟠 Orange (P2)</option>
                                                            <option value="YELLOW" {{ $entry->triage_level === 'YELLOW' ? 'selected' : '' }}>🟡 Yellow (P3)</option>
                                                            <option value="GREEN" {{ $entry->triage_level === 'GREEN' ? 'selected' : '' }}>🟢 Green (P4)</option>
                                                            <option value="BLUE" {{ $entry->triage_level === 'BLUE' ? 'selected' : '' }}>🔵 Blue (P5)</option>
                                                        </select>
                                                    </form>
                                                </td>
                                                <td>
                                                    @if($entry->allocatedBed)
                                                        <span class="font-mono text-xs font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200">
                                                            🛏️ {{ $entry->allocatedBed->bed_number }}
                                                        </span>
                                                    @else
                                                        <a href="{{ route('staff.beds.index') }}" class="text-[11px] text-slate-400 hover:text-indigo-600 italic">
                                                            + Assign Bay
                                                        </a>
                                                    @endif
                                                </td>
                                                <td class="text-xs text-slate-600">{{ $entry->joined_at->format('g:i A') }}</td>
                                                <td class="text-xs font-semibold text-slate-700">{{ $entry->joined_at->diffForHumans(null, true) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
