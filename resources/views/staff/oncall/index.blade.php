<x-layouts.app title="Doctor On-Call Roster & Emergency Paging">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Doctor On-Call & Duty Roster</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Manage active clinical shift rosters, trauma specialists on standby, and emergency paging.
                </p>
            </div>
            <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary text-xs">
                &larr; Back to Clinical Console
            </a>
        </div>

        {{-- Active On-Call Summary Banner --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="stat-card border-l-4 border-emerald-500">
                <span class="text-xs font-bold text-emerald-700 uppercase block">Active Doctors On-Call</span>
                <span class="text-2xl font-black text-emerald-600 mt-1 block">{{ $activeOnCall->count() }} Clinicians</span>
                <span class="text-[11px] text-slate-400">Available for rapid emergency response</span>
            </div>

            <div class="stat-card border-l-4 border-indigo-500">
                <span class="text-xs font-bold text-indigo-700 uppercase block">Today's Duty Shifts</span>
                <span class="text-2xl font-black text-indigo-600 mt-1 block">{{ $todayRosters->count() }} Scheduled</span>
                <span class="text-[11px] text-slate-400">Day, night and trauma rotations</span>
            </div>

            <div class="stat-card border-l-4 border-slate-700">
                <span class="text-xs font-bold text-slate-500 uppercase block">Total Clinical Staff</span>
                <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $doctors->count() }} Doctors</span>
                <span class="text-[11px] text-slate-400">In hospital medical directory</span>
            </div>
        </div>

        {{-- Doctor Roster Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($doctors as $doc)
                <div class="card p-6 border {{ $doc->is_on_call ? 'border-emerald-300 bg-emerald-50/10 ring-2 ring-emerald-500/20' : 'border-slate-200' }}">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div>
                            <span class="text-[11px] font-mono text-slate-400 block">{{ $doc->hospital_id ?? 'MED-DOC-001' }}</span>
                            <h3 class="text-base font-black text-slate-900 mt-0.5">{{ $doc->name }}</h3>
                            <span class="text-xs text-indigo-700 font-semibold block">{{ $doc->specialization ?? 'General Outpatient Care' }}</span>
                        </div>
                        <span class="badge {{ $doc->is_on_call ? 'badge-success' : 'badge-completed' }}">
                            {{ $doc->is_on_call ? 'ON-CALL' : 'OFF-DUTY' }}
                        </span>
                    </div>

                    <div class="space-y-2 text-xs text-slate-600 mb-6 bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <div><span class="font-semibold text-slate-700">Shift:</span> {{ $doc->on_call_shift ?? 'Standard Roster' }}</div>
                        <div><span class="font-semibold text-slate-700">Contact:</span> {{ $doc->phone ?? 'Internal Hospital Ext' }}</div>
                    </div>

                    {{-- Actions: Toggle On-Call & Emergency Page --}}
                    <div class="space-y-3 pt-3 border-t border-slate-100">
                        {{-- Toggle Status --}}
                        <form method="POST" action="{{ route('staff.oncall.toggle', $doc) }}">
                            @csrf
                            <input type="hidden" name="is_on_call" value="{{ $doc->is_on_call ? 0 : 1 }}">
                            <button type="submit" class="btn {{ $doc->is_on_call ? 'btn-secondary text-rose-600 hover:text-rose-800' : 'btn-primary bg-emerald-600 hover:bg-emerald-700' }} btn-sm w-full justify-center text-xs font-bold">
                                {{ $doc->is_on_call ? 'Set to Off-Duty' : 'Set to Active On-Call' }}
                            </button>
                        </form>

                        {{-- Emergency Page Modal Trigger --}}
                        @if($doc->is_on_call)
                            <form method="POST" action="{{ route('staff.oncall.page', $doc) }}" class="flex gap-2">
                                @csrf
                                <input type="text" name="urgency_reason" placeholder="Reason (e.g. Trauma Bay 1)" class="form-input text-xs py-1 h-8 flex-1" required>
                                <button type="submit" class="btn bg-rose-600 hover:bg-rose-700 text-white btn-sm text-xs font-bold h-8 whitespace-nowrap">
                                    🚨 Page
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.app>
