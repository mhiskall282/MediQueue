<x-layouts.app title="Available Services">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Select a Clinic Service</h1>
            <p class="text-slate-500 text-sm mt-1">Choose the healthcare department you wish to consult with today.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($services as $service)
                <div class="card p-6 flex flex-col justify-between hover:shadow-md hover:border-indigo-200 transition-all duration-200">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-block bg-indigo-50 text-indigo-700 text-xs font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">
                                Prefix: {{ $service->prefix }}
                            </span>
                            <span class="inline-flex items-center gap-1 text-xs text-slate-500 font-medium">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                ~{{ $service->avg_duration_minutes }}m / patient
                            </span>
                        </div>

                        <h2 class="text-lg font-bold text-slate-900 mb-2">{{ $service->name }}</h2>
                        <p class="text-slate-600 text-sm leading-relaxed mb-6">{{ $service->description }}</p>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <div class="grid grid-cols-2 gap-2 mb-4 bg-slate-50 p-3 rounded-lg text-center">
                            <div>
                                <span class="text-[11px] text-slate-500 block uppercase font-medium">Waiting</span>
                                <span class="text-lg font-bold text-slate-800">{{ $service->waiting_count }}</span>
                            </div>
                            <div>
                                <span class="text-[11px] text-slate-500 block uppercase font-medium">Now Serving</span>
                                <span class="text-lg font-bold text-indigo-600">{{ $service->currently_serving ?? '—' }}</span>
                            </div>
                        </div>

                        <a href="{{ route('patient.queue.show', $service) }}" class="btn btn-primary w-full justify-center">
                            Select & Join Queue
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center card p-8">
                    <p class="text-slate-500 text-sm">No active clinic services are available at this moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
