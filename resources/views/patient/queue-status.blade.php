<x-layouts.app title="Queue Status - {{ $queueEntry->queue_number }}">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="text-center mb-8">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200 mb-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Live Queue Ticket
            </span>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">{{ $queueEntry->service->name }}</h1>
            <p class="text-slate-500 text-sm mt-1">Ticket issued on {{ $queueEntry->joined_at->format('M d, Y \a\t g:i A') }}</p>
        </div>

        {{-- Main Ticket Display Card --}}
        <div class="card p-8 text-center shadow-lg border-indigo-100 relative overflow-hidden">
            {{-- Big Ticket Number --}}
            <div class="my-6">
                <span class="text-xs uppercase font-bold tracking-widest text-slate-400 block mb-1">Your Ticket Number</span>
                <div class="queue-number font-black text-6xl sm:text-7xl text-indigo-600 tracking-tight" id="queue-number">
                    {{ $queueEntry->queue_number }}
                </div>
                <div class="mt-4">
                    <span class="badge {{ $queueEntry->status_badge_class }} text-sm px-3.5 py-1" id="status-badge">
                        {{ $queueEntry->status_label }}
                    </span>
                </div>
            </div>

            {{-- Live Queue Metrics (for WAITING status) --}}
            <div id="waiting-metrics" class="{{ $queueEntry->status === 'WAITING' ? 'block' : 'hidden' }}">
                <div class="grid grid-cols-3 gap-3 my-8 bg-slate-50 p-5 rounded-2xl border border-slate-100">
                    <div class="border-r border-slate-200 pr-2">
                        <span class="text-xs text-slate-500 font-medium block uppercase">Your Position</span>
                        <span class="text-2xl sm:text-3xl font-black text-slate-900" id="queue-position">
                            #{{ $positionData['position'] }}
                        </span>
                    </div>
                    <div class="border-r border-slate-200 px-2">
                        <span class="text-xs text-slate-500 font-medium block uppercase">People Ahead</span>
                        <span class="text-2xl sm:text-3xl font-black text-indigo-600" id="people-ahead">
                            {{ $positionData['people_ahead'] }}
                        </span>
                    </div>
                    <div class="pl-2">
                        <span class="text-xs text-slate-500 font-medium block uppercase">Est. Wait</span>
                        <span class="text-2xl sm:text-3xl font-black text-slate-900" id="estimated-wait">
                            ~{{ $estimatedWait }}m
                        </span>
                    </div>
                </div>

                <div class="text-xs text-slate-500 mb-6 bg-indigo-50/40 p-3 rounded-lg flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Now serving in this department: <strong id="now-serving">{{ $positionData['currently_serving'] ?? 'None yet' }}</strong>
                </div>
            </div>

            {{-- CALLED state alert banner --}}
            <div id="called-banner" class="{{ $queueEntry->status === 'CALLED' ? 'block' : 'hidden' }} my-6 p-6 bg-indigo-600 text-white rounded-2xl text-center shadow-lg animate-bounce">
                <h2 class="text-xl font-black uppercase tracking-wide">It is Your Turn!</h2>
                <p class="text-indigo-100 text-sm mt-1">Please proceed immediately to the consultation counter.</p>
            </div>

            {{-- IN_SERVICE state alert banner --}}
            <div id="serving-banner" class="{{ $queueEntry->status === 'IN_SERVICE' ? 'block' : 'hidden' }} my-6 p-6 bg-emerald-600 text-white rounded-2xl text-center shadow-lg">
                <h2 class="text-xl font-bold uppercase tracking-wide">Consultation In Progress</h2>
                <p class="text-emerald-100 text-sm mt-1">You are currently receiving service from the clinical team.</p>
            </div>

            {{-- COMPLETED state alert banner --}}
            <div id="completed-banner" class="{{ $queueEntry->status === 'COMPLETED' ? 'block' : 'hidden' }} my-6 p-6 bg-slate-100 text-slate-800 rounded-2xl text-center border border-slate-200">
                <h2 class="text-lg font-bold">Service Completed</h2>
                <p class="text-slate-500 text-xs mt-1">Thank you for visiting MediQueue. Have a pleasant day.</p>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('patient.dashboard') }}" class="btn btn-secondary w-full sm:w-auto">
                    Return to Dashboard
                </a>

                @if($queueEntry->status === 'WAITING')
                    <form method="POST" action="{{ route('patient.queue.cancel', $queueEntry) }}" onsubmit="return confirm('Are you sure you want to cancel your queue place?');" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit" class="btn btn-ghost text-rose-600 hover:bg-rose-50 w-full sm:w-auto">
                            Cancel Queue Ticket
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Automatic polling for real-time status update without full reload
        const statusUrl = "{{ route('patient.queue.status.json', $queueEntry) }}";
        const pollIntervalMs = 4000;

        function updateQueueStatus() {
            fetch(statusUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                const statusBadge = document.getElementById('status-badge');
                if (statusBadge) {
                    statusBadge.textContent = data.status_label;
                }

                if (data.status === 'WAITING') {
                    document.getElementById('waiting-metrics').classList.remove('hidden');
                    document.getElementById('called-banner').classList.add('hidden');
                    document.getElementById('serving-banner').classList.add('hidden');
                    document.getElementById('completed-banner').classList.add('hidden');

                    document.getElementById('queue-position').textContent = '#' + data.position;
                    document.getElementById('people-ahead').textContent = data.people_ahead;
                    document.getElementById('estimated-wait').textContent = '~' + data.estimated_wait + 'm';
                    document.getElementById('now-serving').textContent = data.currently_serving || 'None yet';
                } else if (data.status === 'CALLED') {
                    document.getElementById('waiting-metrics').classList.add('hidden');
                    document.getElementById('called-banner').classList.remove('hidden');
                    document.getElementById('serving-banner').classList.add('hidden');
                    document.getElementById('completed-banner').classList.add('hidden');
                } else if (data.status === 'IN_SERVICE') {
                    document.getElementById('waiting-metrics').classList.add('hidden');
                    document.getElementById('called-banner').classList.add('hidden');
                    document.getElementById('serving-banner').classList.remove('hidden');
                    document.getElementById('completed-banner').classList.add('hidden');
                } else if (data.status === 'COMPLETED' || data.status === 'CANCELLED' || data.status === 'SKIPPED') {
                    document.getElementById('waiting-metrics').classList.add('hidden');
                    document.getElementById('called-banner').classList.add('hidden');
                    document.getElementById('serving-banner').classList.add('hidden');
                    document.getElementById('completed-banner').classList.remove('hidden');
                }
            })
            .catch(err => console.error("Poll error:", err));
        }

        // Run poll every 4 seconds
        setInterval(updateQueueStatus, pollIntervalMs);
    </script>
    @endpush
</x-layouts.app>
