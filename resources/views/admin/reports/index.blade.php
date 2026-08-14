<x-layouts.app title="Clinic Reports & Operational Analytics">
    <div class="max-w-7xl mx-auto space-y-8">
        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Clinical Reports & Operational Analytics</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Operational analytics, attendance tracking, staff consultation times, and forensic investigation audit.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                {{-- Email Report Form --}}
                <form method="POST" action="{{ route('admin.reports.email') }}" class="inline-block">
                    @csrf
                    <input type="hidden" name="start_date" value="{{ $startDate }}">
                    <input type="hidden" name="end_date" value="{{ $endDate }}">
                    <button type="submit" class="btn btn-secondary text-xs font-bold flex items-center gap-1.5 border-slate-300">
                        <span>✉️</span> Email Summary to Admin
                    </button>
                </form>

                {{-- Export CSV Button --}}
                <a href="{{ route('admin.reports.export', request()->all()) }}" class="btn btn-secondary text-xs font-bold flex items-center gap-1.5 border-slate-300">
                    <span>📊</span> Export to CSV
                </a>

                {{-- Export PDF Button --}}
                <a href="{{ route('admin.reports.export-pdf', request()->all()) }}" target="_blank" class="btn btn-primary text-xs font-bold flex items-center gap-1.5 shadow-md">
                    <span>📄</span> Export to PDF
                </a>
            </div>
        </div>

        {{-- Filter Card --}}
        <div class="card p-6">
            <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="form-label text-xs">Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="form-input text-xs">
                </div>

                <div>
                    <label class="form-label text-xs">End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="form-input text-xs">
                </div>

                <div>
                    <label class="form-label text-xs">Department / Service</label>
                    <select name="service_id" class="form-input text-xs">
                        <option value="">All Departments</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ $serviceId == $service->id ? 'selected' : '' }}>
                                {{ $service->name }} ({{ $service->prefix }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label text-xs">Attending Staff</label>
                    <select name="staff_id" class="form-input text-xs">
                        <option value="">All Staff Members</option>
                        @foreach($staffMembers as $staff)
                            <option value="{{ $staff->id }}" {{ $staffId == $staff->id ? 'selected' : '' }}>
                                {{ $staff->name }} ({{ $staff->specialization ?? 'Clinical Staff' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="btn btn-primary w-full text-xs font-bold">
                        Filter Records
                    </button>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary text-xs">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Metric Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="stat-card border-l-4 border-indigo-500">
                <span class="text-xs font-bold text-slate-500 uppercase block">Total Volume</span>
                <span class="text-2xl font-black text-slate-900 mt-1 block">{{ number_format($totalEntries) }}</span>
                <span class="text-[11px] text-slate-400">Patients registered</span>
            </div>

            <div class="stat-card border-l-4 border-emerald-500">
                <span class="text-xs font-bold text-emerald-600 uppercase block">Completed</span>
                <span class="text-2xl font-black text-emerald-600 mt-1 block">{{ number_format($completedEntries) }}</span>
                <span class="text-[11px] text-slate-400">{{ $totalEntries > 0 ? round(($completedEntries / $totalEntries) * 100) : 0 }}% completion rate</span>
            </div>

            <div class="stat-card border-l-4 border-orange-500">
                <span class="text-xs font-bold text-orange-600 uppercase block">Skipped No-Shows</span>
                <span class="text-2xl font-black text-orange-600 mt-1 block">{{ number_format($skippedEntries) }}</span>
                <span class="text-[11px] text-slate-400">Unanswered callouts</span>
            </div>

            <div class="stat-card border-l-4 border-indigo-500">
                <span class="text-xs font-bold text-indigo-600 uppercase block">Avg Wait Duration</span>
                <span class="text-2xl font-black text-indigo-600 mt-1 block">{{ round($avgWaitMinutes) }} min</span>
                <span class="text-[11px] text-slate-400">From join to call</span>
            </div>

            <div class="stat-card border-l-4 border-purple-500">
                <span class="text-xs font-bold text-purple-600 uppercase block">Avg Consultation</span>
                <span class="text-2xl font-black text-purple-600 mt-1 block">{{ round($avgServiceMinutes) }} min</span>
                <span class="text-[11px] text-slate-400">Time spent with doctor</span>
            </div>
        </div>

        {{-- Visual Charts for Analytics Breakdown --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="card p-6 shadow-sm">
                <h3 class="text-sm font-black text-slate-900 mb-1">Consultation Outcome Distribution</h3>
                <p class="text-xs text-slate-500 mb-4">Breakdown of patient consultation statuses for the period.</p>
                <div class="h-52 w-full relative">
                    <canvas id="outcomesChart"></canvas>
                </div>
            </div>

            <div class="lg:col-span-2 card p-6 shadow-sm">
                <h3 class="text-sm font-black text-slate-900 mb-1">Performance Overview & KPIs</h3>
                <p class="text-xs text-slate-500 mb-4">Patient throughput and operational benchmarks.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-xs font-bold text-slate-500 uppercase block">Total Clinical Hours Delivered</span>
                        <span class="text-2xl font-black text-indigo-700 mt-1 block">
                            {{ round(($completedEntries * $avgServiceMinutes) / 60, 1) }} hrs
                        </span>
                        <span class="text-[11px] text-slate-400">Direct patient care time</span>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-xs font-bold text-slate-500 uppercase block">Service Reliability Ratio</span>
                        <span class="text-2xl font-black text-emerald-600 mt-1 block">
                            {{ $totalEntries > 0 ? round((($completedEntries) / ($totalEntries)) * 100, 1) : 100 }}%
                        </span>
                        <span class="text-[11px] text-slate-400">Attended vs Abandoned/Skipped</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Records Table --}}
        <div class="card overflow-hidden shadow-sm">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-600">
                    Clinical Activity Log ({{ $entries->total() }} total entries)
                </span>
            </div>

            @if($entries->isEmpty())
                <div class="p-12 text-center text-slate-500 text-sm">
                    No clinical queue records match the specified search filters.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Ticket #</th>
                                <th>MRN / ID</th>
                                <th>Patient Name</th>
                                <th>Department</th>
                                <th>Attending Clinician</th>
                                <th>Triage</th>
                                <th>Wait Time</th>
                                <th>Service Time</th>
                                <th>Status</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entries as $entry)
                                <tr>
                                    <td class="text-xs text-slate-600">
                                        {{ $entry->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="font-mono font-bold text-xs text-indigo-700">
                                        {{ $entry->queue_number }}
                                    </td>
                                    <td class="font-mono text-xs text-slate-500">
                                        {{ $entry->hospital_id ?? 'MRN-' . $entry->patient_id }}
                                    </td>
                                    <td>
                                        <span class="font-bold text-slate-900">{{ $entry->patient->name }}</span>
                                    </td>
                                    <td>
                                        {{ $entry->service->name }}
                                    </td>
                                    <td>
                                        {{ $entry->servedBy->name ?? '-' }}
                                    </td>
                                    <td>
                                        <span class="badge text-[10px] {{ $entry->triage_badge_class }}">
                                            {{ $entry->triage_level }}
                                        </span>
                                    </td>
                                    <td class="text-xs">
                                        @if($entry->service_started_at)
                                            {{ $entry->joined_at->diffInMinutes($entry->service_started_at) }} mins
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-xs">
                                        @if($entry->service_started_at && $entry->completed_at)
                                            {{ $entry->service_started_at->diffInMinutes($entry->completed_at) }} mins
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $entry->status_badge_class }}">
                                            {{ $entry->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('admin.reports.investigate', $entry) }}" class="btn btn-secondary btn-sm text-xs font-bold text-indigo-700">
                                            🔍 Investigate
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $entries->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const outcomeCtx = document.getElementById('outcomesChart');
            if (outcomeCtx) {
                new Chart(outcomeCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Completed', 'Skipped (No-Show)', 'Other Active'],
                        datasets: [{
                            data: [
                                {{ $completedEntries }},
                                {{ $skippedEntries }},
                                {{ max(0, $totalEntries - ($completedEntries + $skippedEntries)) }}
                            ],
                            backgroundColor: ['#16a34a', '#ea580c', '#6366f1'],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { boxWidth: 10, font: { size: 10 } }
                            }
                        },
                        cutout: '60%'
                    }
                });
            }
        });
    </script>
    @endpush
</x-layouts.app>
