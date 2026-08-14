<x-layouts.app title="Hospital Bed & Ward Capacity Management">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Hospital Bed & Bay Management</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Live ward occupancy, triage rapid-assessment bays, ICU bed allocations, and patient transfers.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary text-xs">
                    &larr; Back to Clinical Console
                </a>
            </div>
        </div>

        {{-- Occupancy Metrics --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="stat-card border-l-4 border-indigo-600">
                <span class="text-xs font-bold text-slate-500 uppercase block">Total Ward Capacity</span>
                <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $totalBeds }} Beds & Bays</span>
                <span class="text-[11px] text-slate-400">Across all clinic wings</span>
            </div>

            <div class="stat-card border-l-4 border-emerald-500">
                <span class="text-xs font-bold text-emerald-600 uppercase block">Available Beds</span>
                <span class="text-2xl font-black text-emerald-600 mt-1 block">{{ $availableBeds }} Ready</span>
                <span class="text-[11px] text-slate-400">Available for immediate patient intake</span>
            </div>

            <div class="stat-card border-l-4 border-rose-500">
                <span class="text-xs font-bold text-rose-600 uppercase block">Occupied / In Use</span>
                <span class="text-2xl font-black text-rose-600 mt-1 block">{{ $occupiedBeds }} Occupied</span>
                <span class="text-[11px] text-slate-400">{{ $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100) : 0 }}% occupancy rate</span>
            </div>
        </div>

        {{-- Bed Grid by Wards --}}
        <div class="card p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span>🛏️</span> Live Ward Floor Plan & Bed Status
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($beds as $bed)
                    <div class="border rounded-2xl p-4 transition-all {{ $bed->isAvailable() ? 'bg-emerald-50/30 border-emerald-200 hover:border-emerald-400' : 'bg-rose-50/30 border-rose-200 hover:border-rose-400' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-mono font-black text-base {{ $bed->isAvailable() ? 'text-emerald-800' : 'text-rose-900' }}">
                                {{ $bed->bed_number }}
                            </span>
                            <span class="badge text-[10px] {{ $bed->status_badge_class }}">
                                {{ $bed->status }}
                            </span>
                        </div>

                        <div class="text-xs font-bold text-slate-700 mb-1">{{ $bed->ward_name }}</div>
                        <div class="text-[11px] text-slate-500 mb-3 flex items-center gap-1">
                            <span class="font-semibold text-indigo-700">Type:</span> {{ $bed->bed_type }}
                        </div>

                        @if($bed->status === 'OCCUPIED' && $bed->currentPatient)
                            <div class="p-2.5 bg-white rounded-xl border border-rose-200 text-xs mb-3 shadow-xs">
                                <span class="text-[10px] font-bold text-rose-700 uppercase block mb-0.5">Current Patient</span>
                                <div class="font-bold text-slate-900">{{ $bed->currentPatient->name }}</div>
                                <div class="text-[11px] text-slate-400">ID: #{{ $bed->current_patient_id }}</div>
                            </div>
                        @else
                            <div class="p-2.5 bg-white/60 rounded-xl border border-emerald-100 text-xs mb-3 text-emerald-700 font-medium">
                                Ready for allocation
                            </div>
                        @endif

                        @if($bed->isAvailable() && $activeEntries->isNotEmpty())
                            {{-- Quick Allocate Form --}}
                            <form method="POST" action="" id="allocate-form-{{ $bed->id }}">
                                @csrf
                                <div class="flex items-center gap-1.5">
                                    <select onchange="this.form.action='/staff/queue/' + this.value + '/allocate-bed'" class="form-input text-xs py-1 px-2 h-8" required>
                                        <option value="">Select Patient...</option>
                                        @foreach($activeEntries as $entry)
                                            <option value="{{ $entry->id }}">
                                                {{ $entry->queue_number }} — {{ $entry->patient->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="bed_id" value="{{ $bed->id }}">
                                    <button type="submit" class="btn btn-primary btn-sm text-[11px] whitespace-nowrap h-8 px-2.5">
                                        Assign
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.app>
