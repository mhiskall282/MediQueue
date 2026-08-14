<x-layouts.app title="Staff Queue Console">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header & Department Selector --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200 mb-2">
                    Staff Clinical Console
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Queue Management</h1>
            </div>

            {{-- Service Dropdown Switcher --}}
            <form method="GET" action="{{ route('staff.dashboard') }}" class="flex items-center gap-3">
                <label for="service_id" class="text-xs font-bold text-slate-600 uppercase">Department:</label>
                <select name="service_id" id="service_id" onchange="this.form.submit()" class="form-input text-sm py-2 px-3 bg-white font-semibold text-slate-800 rounded-lg shadow-sm border-slate-300">
                    @foreach($services as $svc)
                        <option value="{{ $svc->id }}" {{ $selectedService && $selectedService->id === $svc->id ? 'selected' : '' }}>
                            {{ $svc->name }} ({{ $svc->prefix }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        @if(!$selectedService)
            <div class="card p-12 text-center">
                <p class="text-slate-500">No active clinic services are configured. Please contact the administrator.</p>
            </div>
        @else
            {{-- Metric Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="card p-5 bg-amber-50/50 border-amber-200">
                    <span class="text-xs font-bold uppercase text-amber-700 block">Waiting Queue</span>
                    <span class="text-3xl font-black text-amber-900 mt-1 block">{{ $waitingEntries->count() }}</span>
                </div>
                <div class="card p-5 bg-indigo-50/50 border-indigo-200">
                    <span class="text-xs font-bold uppercase text-indigo-700 block">Called / Serving</span>
                    <span class="text-3xl font-black text-indigo-900 mt-1 block">{{ $calledEntries->count() }}</span>
                </div>
                <div class="card p-5 bg-emerald-50/50 border-emerald-200">
                    <span class="text-xs font-bold uppercase text-emerald-700 block">Completed Today</span>
                    <span class="text-3xl font-black text-emerald-900 mt-1 block">{{ $completedToday }}</span>
                </div>
                <div class="card p-5 bg-slate-50 border-slate-200">
                    <span class="text-xs font-bold uppercase text-slate-600 block">Avg Wait Time</span>
                    <span class="text-3xl font-black text-slate-800 mt-1 block">~{{ $avgWaitMinutes }} min</span>
                </div>
            </div>

            {{-- Staff Operation Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Left: Active & Called Console (Primary Focus) --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- "Call Next" Primary CTA Card --}}
                    <div class="card p-6 bg-gradient-to-br from-indigo-900 to-indigo-800 text-white shadow-md">
                        <h2 class="text-base font-bold text-indigo-100 uppercase tracking-wider">Queue Action</h2>
                        <p class="text-xs text-indigo-200 mt-1 mb-6">Call the next eligible patient according to queue sequence and priority.</p>

                        <form method="POST" action="{{ route('staff.queue.call-next') }}">
                            @csrf
                            <input type="hidden" name="service_id" value="{{ $selectedService->id }}">
                            <button type="submit" class="btn btn-xl w-full bg-emerald-500 hover:bg-emerald-600 text-white font-black text-lg justify-center shadow-lg border-0 {{ $waitingEntries->isEmpty() ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $waitingEntries->isEmpty() ? 'disabled' : '' }}>
                                <svg class="w-6 h-6 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                Call Next Patient
                            </button>
                        </form>
                    </div>

                    {{-- Currently Called / In-Service Tickets --}}
                    <div class="card p-6">
                        <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                            Active Consultations ({{ $calledEntries->count() }})
                        </h2>

                        @if($calledEntries->isEmpty())
                            <p class="text-xs text-slate-400 text-center py-6">No patients currently called or in service.</p>
                        @else
                            <div class="space-y-4">
                                @foreach($calledEntries as $entry)
                                    <div class="border border-indigo-100 bg-indigo-50/40 rounded-xl p-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-2xl font-black text-indigo-700 font-mono">{{ $entry->queue_number }}</span>
                                            <span class="badge {{ $entry->status_badge_class }}">{{ $entry->status_label }}</span>
                                        </div>
                                        <p class="text-xs font-bold text-slate-800 mt-2">{{ $entry->patient->name }}</p>
                                        <p class="text-[11px] text-slate-500">Called at: {{ $entry->called_at?->format('g:i A') }}</p>

                                        {{-- Actions based on state --}}
                                        <div class="mt-4 pt-3 border-t border-indigo-100 flex flex-wrap gap-2">
                                            @if($entry->status === 'CALLED')
                                                <form method="POST" action="{{ route('staff.queue.start', $entry) }}" class="flex-1">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm w-full justify-center">
                                                        Start Service
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('staff.queue.skip', $entry) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-ghost btn-sm text-amber-700 hover:bg-amber-100" title="Patient did not respond">
                                                        Skip
                                                    </button>
                                                </form>
                                            @elseif($entry->status === 'IN_SERVICE')
                                                <form method="POST" action="{{ route('staff.queue.complete', $entry) }}" class="w-full">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary btn-sm w-full justify-center">
                                                        Complete Service
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

                {{-- Right: Waiting Patients Table --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="card overflow-hidden">
                        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">Waiting Patients Queue</h2>
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
                                            <th>Priority</th>
                                            <th>Joined</th>
                                            <th>Wait Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($waitingEntries as $idx => $entry)
                                            <tr>
                                                <td class="font-bold text-slate-500">#{{ $idx + 1 }}</td>
                                                <td class="font-black text-indigo-700 font-mono">{{ $entry->queue_number }}</td>
                                                <td class="font-medium text-slate-900">{{ $entry->patient->name }}</td>
                                                <td>
                                                    @if($entry->priority === 'URGENT')
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">URGENT</span>
                                                    @else
                                                        <span class="text-xs text-slate-500">Normal</span>
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
