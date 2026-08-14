<x-layouts.app title="Join Queue - {{ $service->name }}">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <a href="{{ route('patient.queue.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-indigo-600 mb-6 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Services
        </a>

        <div class="card p-8">
            <div class="border-b border-slate-100 pb-6 mb-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded">
                            Department Code: {{ $service->prefix }}
                        </span>
                        <h1 class="text-2xl font-bold text-slate-900 mt-2">{{ $service->name }}</h1>
                    </div>
                </div>
                <p class="text-sm text-slate-600 mt-2 leading-relaxed">{{ $service->description }}</p>
            </div>

            {{-- Live Service Metrics --}}
            <div class="grid grid-cols-3 gap-4 mb-8 bg-slate-50 p-4 rounded-xl text-center border border-slate-100">
                <div>
                    <span class="text-xs text-slate-500 block font-medium">Patients Ahead</span>
                    <span class="text-xl font-bold text-slate-800">{{ $waitingCount }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block font-medium">Estimated Wait</span>
                    <span class="text-xl font-bold text-slate-800">~{{ $estimatedWait }} min</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block font-medium">Currently Serving</span>
                    <span class="text-xl font-bold text-indigo-600">{{ $currentlyServing ?? 'None' }}</span>
                </div>
            </div>

            @if($existingEntry)
                <div class="alert alert-warning mb-6">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="text-sm">
                        <p class="font-bold">Active Queue Ticket Already Issued</p>
                        <p class="mt-0.5">You currently hold ticket <strong>{{ $existingEntry->queue_number }}</strong> ({{ $existingEntry->status_label }}) for this service.</p>
                        <div class="mt-3">
                            <a href="{{ route('patient.queue.status', $existingEntry) }}" class="btn btn-primary btn-sm">
                                View Your Ticket Status
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <form method="POST" action="{{ route('patient.queue.store') }}">
                    @csrf
                    <input type="hidden" name="service_id" value="{{ $service->id }}">

                    <div class="p-4 bg-indigo-50/50 border border-indigo-100 rounded-xl mb-6 text-sm text-slate-700 space-y-2">
                        <h2 class="font-bold text-slate-900 text-sm">Please Note Before Joining:</h2>
                        <ul class="list-disc list-inside space-y-1 text-xs text-slate-600">
                            <li>You will be assigned the next sequential ticket number atomically.</li>
                            <li>You can monitor your live position on your smartphone or browser.</li>
                            <li>Please be near the consultation room once there are fewer than 2 people ahead.</li>
                        </ul>
                    </div>

                    <button type="submit" class="btn btn-primary w-full py-3.5 text-base justify-center font-semibold shadow-md">
                        Confirm & Issue Queue Ticket
                    </button>
                </form>
            @endif
        </div>
    </div>
</x-layouts.app>
