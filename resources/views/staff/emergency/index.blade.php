<x-layouts.app title="Emergency Trauma & Rapid Unconscious Intake">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200 mb-2">
                    <span class="w-2 h-2 rounded-full bg-rose-600 animate-ping"></span>
                    🚨 CODE RED TRAUMA PROTOCOL
                </div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Emergency Trauma & Rapid Intake</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Rapid admission for unconscious/unidentified patients, automatic Red Triage P1 assignment, bay allocation, and on-call paging.
                </p>
            </div>
            <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary text-xs">
                &larr; Back to Clinical Console
            </a>
        </div>

        {{-- Emergency Intake Form & Active Trauma Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column: Rapid Intake Form --}}
            <div class="card p-6 border-2 border-rose-500/30 bg-rose-50/10 shadow-lg">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-2xl">🚑</span>
                    <div>
                        <h2 class="text-base font-black text-slate-900">Rapid Unconscious Patient Admission</h2>
                        <p class="text-xs text-slate-500">Generates temporary Trauma ID & pages on-call team.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('staff.emergency.unconscious-intake') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="form-label text-xs">Estimated Biological Gender *</label>
                        <select name="estimated_gender" class="form-input text-xs" required>
                            <option value="UNKNOWN">Unknown / Unspecified</option>
                            <option value="MALE">Male (John Doe)</option>
                            <option value="FEMALE">Female (Jane Doe)</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label text-xs">Initial Clinical Assessment & Circumstances *</label>
                        <textarea name="intake_notes" rows="3" class="form-input text-xs" required placeholder="e.g. Unconscious pedestrian struck by vehicle, GCS 7, unreactive pupils..."></textarea>
                    </div>

                    <div>
                        <label class="form-label text-xs">Emergency Vital Signs (BP, HR, SpO2, GCS)</label>
                        <input type="text" name="vital_signs" class="form-input text-xs" placeholder="e.g. BP 85/50, HR 135 bpm, SpO2 88%, GCS 6/15">
                    </div>

                    <div>
                        <label class="form-label text-xs">Assign Emergency Triage Bay</label>
                        <select name="allocated_bay_id" class="form-input text-xs">
                            <option value="">Auto-assign first available bay</option>
                            @foreach($triageBays as $bay)
                                <option value="{{ $bay->id }}" {{ $bay->isAvailable() ? '' : 'disabled' }}>
                                    {{ $bay->bed_number }} — {{ $bay->ward_name }} ({{ $bay->status }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn bg-rose-600 hover:bg-rose-700 text-white w-full py-3 text-xs font-black shadow-md flex items-center justify-center gap-2">
                        <span>🚨</span> EXECUTE CODE RED INTAKE
                    </button>
                </form>
            </div>

            {{-- Right Column: Active Emergency Trauma Patients List --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="card overflow-hidden">
                    <div class="p-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700">
                            Active Trauma Admissions ({{ $emergencyEntries->count() }})
                        </h2>
                    </div>

                    @if($emergencyEntries->isEmpty())
                        <div class="p-12 text-center text-slate-400 text-sm">
                            No active emergency unconscious admissions currently in care.
                        </div>
                    @else
                        <div class="divide-y divide-slate-100">
                            @foreach($emergencyEntries as $entry)
                                <div class="p-6 bg-white hover:bg-slate-50/50 transition-colors">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-3">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-black text-rose-700 text-xl font-mono">{{ $entry->queue_number }}</span>
                                                <span class="badge bg-red-600 text-white font-black text-[10px] animate-pulse">RED (P1)</span>
                                                <span class="font-mono text-xs font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded">{{ $entry->hospital_id }}</span>
                                            </div>
                                            <h3 class="font-bold text-slate-900 text-base mt-1">{{ $entry->patient->name }}</h3>
                                        </div>

                                        @if($entry->allocatedBed)
                                            <div class="text-xs font-bold text-indigo-700 bg-indigo-50 px-3 py-1 rounded-xl border border-indigo-200">
                                                🛏️ Bay {{ $entry->allocatedBed->bed_number }} ({{ $entry->allocatedBed->ward_name }})
                                            </div>
                                        @endif
                                    </div>

                                    <div class="p-3 bg-slate-50 rounded-xl text-xs text-slate-700 mb-4 whitespace-pre-line border border-slate-100">
                                        {{ $entry->triage_notes }}
                                    </div>

                                    {{-- Link Verified Patient ID Form --}}
                                    <form method="POST" action="{{ route('staff.emergency.link-permanent-id', $entry) }}" class="p-3 bg-indigo-50/30 rounded-xl border border-indigo-100 flex flex-wrap items-center gap-2">
                                        @csrf
                                        <span class="text-[11px] font-bold text-indigo-900 w-full sm:w-auto">🔗 Link Verified Identity:</span>
                                        <input type="number" name="permanent_user_id" placeholder="Permanent User ID" class="form-input text-xs py-1 h-8 flex-1" required>
                                        <input type="text" name="verified_mrn" placeholder="Verified Hospital MRN" class="form-input text-xs py-1 h-8 flex-1" required>
                                        <button type="submit" class="btn btn-secondary btn-sm text-xs font-bold text-indigo-700 h-8">
                                            Link Record
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
