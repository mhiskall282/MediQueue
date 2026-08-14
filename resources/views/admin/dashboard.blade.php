<x-layouts.app title="Hospital Executive Overview & System Dashboard">
    <div class="max-w-7xl mx-auto space-y-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 mb-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
                    Hospital Operational Analytics & Real-Time Telemetry
                </div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Executive Dashboard</h1>
                <p class="text-sm text-slate-500 mt-1">Live patient throughput, clinical triage distributions, bed capacity, and department performance.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary text-xs font-bold flex items-center gap-1.5 border-slate-300">
                    <span>📈</span> Operational Reports & CSV
                </a>
                <a href="{{ route('admin.services.create') }}" class="btn btn-primary text-xs font-bold flex items-center gap-1.5 shadow-xs">
                    <span>+</span> New Department
                </a>
            </div>
        </div>

        {{-- Top KPI Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="stat-card border-l-4 border-indigo-500">
                <span class="text-[11px] font-bold uppercase text-slate-500 block">Total Patients</span>
                <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $stats['total_patients'] }}</span>
                <span class="text-[10px] text-slate-400">Registered MRNs</span>
            </div>

            <div class="stat-card border-l-4 border-emerald-500">
                <span class="text-[11px] font-bold uppercase text-emerald-700 block">On-Call Staff</span>
                <span class="text-2xl font-black text-emerald-600 mt-1 block">{{ $stats['active_on_call'] }} / {{ $stats['total_staff'] }}</span>
                <span class="text-[10px] text-slate-400">Active clinicians</span>
            </div>

            <div class="stat-card border-l-4 border-amber-500">
                <span class="text-[11px] font-bold uppercase text-amber-700 block">Waiting in Line</span>
                <span class="text-2xl font-black text-amber-600 mt-1 block">{{ $stats['waiting_now'] }}</span>
                <span class="text-[10px] text-slate-400">Across all wings</span>
            </div>

            <div class="stat-card border-l-4 border-blue-500">
                <span class="text-[11px] font-bold uppercase text-blue-700 block">In Consultation</span>
                <span class="text-2xl font-black text-blue-600 mt-1 block">{{ $stats['in_service_now'] }}</span>
                <span class="text-[10px] text-slate-400">With doctors / labs</span>
            </div>

            <div class="stat-card border-l-4 border-purple-500">
                <span class="text-[11px] font-bold uppercase text-purple-700 block">Bed Occupancy</span>
                <span class="text-2xl font-black text-purple-600 mt-1 block">{{ $bedStats['occupancy_rate'] }}%</span>
                <span class="text-[10px] text-slate-400">{{ $bedStats['occupied'] }} / {{ $bedStats['total'] }} Beds</span>
            </div>

            <div class="stat-card border-l-4 border-teal-500">
                <span class="text-[11px] font-bold uppercase text-teal-700 block">Avg Wait Time</span>
                <span class="text-2xl font-black text-teal-600 mt-1 block">~{{ $stats['avg_wait_minutes'] }}m</span>
                <span class="text-[10px] text-slate-400">Target &lt; 20 mins</span>
            </div>
        </div>

        {{-- Visual Analytics & Interactive Charts Row --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left 2 Cols: Hourly Arrival & Wait Times Trend --}}
            <div class="lg:col-span-2 card p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-black text-slate-900">Today's Patient Volume & Hourly Inflow</h2>
                        <p class="text-xs text-slate-500">Patient arrival frequency throughout operating hours (8:00 AM – 6:00 PM).</p>
                    </div>
                    <span class="text-[11px] font-mono text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100 font-bold">
                        Total: {{ $stats['total_today'] }} Visits
                    </span>
                </div>
                <div class="h-64 w-full relative">
                    <canvas id="hourlyTrendChart"></canvas>
                </div>
            </div>

            {{-- Right 1 Col: Triage Severity Distribution (Doughnut Chart) --}}
            <div class="card p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-base font-black text-slate-900">Triage Severity</h2>
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Manchester Protocol</span>
                    </div>
                    <p class="text-xs text-slate-500 mb-4">Patient urgency distribution across 5 clinical severity categories.</p>
                </div>

                <div class="h-52 w-full relative my-auto">
                    <canvas id="triageDoughnutChart"></canvas>
                </div>

                <div class="grid grid-cols-5 gap-1 text-center pt-4 border-t border-slate-100 text-[10px] font-bold">
                    <div class="text-red-700">🔴 {{ $triageCounts['RED'] }}<br/><span class="text-[9px] text-slate-400 font-normal">P1 Resus</span></div>
                    <div class="text-orange-700">🟠 {{ $triageCounts['ORANGE'] }}<br/><span class="text-[9px] text-slate-400 font-normal">P2 Very</span></div>
                    <div class="text-yellow-700">🟡 {{ $triageCounts['YELLOW'] }}<br/><span class="text-[9px] text-slate-400 font-normal">P3 Urgent</span></div>
                    <div class="text-emerald-700">🟢 {{ $triageCounts['GREEN'] }}<br/><span class="text-[9px] text-slate-400 font-normal">P4 Normal</span></div>
                    <div class="text-blue-700">🔵 {{ $triageCounts['BLUE'] }}<br/><span class="text-[9px] text-slate-400 font-normal">P5 Non</span></div>
                </div>
            </div>
        </div>

        {{-- Department Performance Table & Bed Status Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left: Department Performance --}}
            <div class="lg:col-span-2 card overflow-hidden shadow-sm">
                <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-black text-slate-900">Departmental Workload & Throughput</h2>
                        <p class="text-xs text-slate-500">Live ticket breakdown by clinical service.</p>
                    </div>
                    <a href="{{ route('admin.services.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                        Manage Services &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Department Name</th>
                                <th>Code</th>
                                <th>Avg Time</th>
                                <th>Waiting</th>
                                <th>Completed</th>
                                <th>Total Tickets</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($serviceBreakdown as $svc)
                                <tr>
                                    <td class="font-bold text-slate-900">{{ $svc->name }}</td>
                                    <td>
                                        <span class="font-mono text-xs font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded">
                                            {{ $svc->prefix }}
                                        </span>
                                    </td>
                                    <td class="text-xs text-slate-600">{{ $svc->avg_duration_minutes }} mins</td>
                                    <td>
                                        <span class="font-bold text-amber-600 {{ $svc->waiting_count > 0 ? 'bg-amber-50 px-2 py-0.5 rounded' : '' }}">
                                            {{ $svc->waiting_count }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="font-bold text-emerald-600">
                                            {{ $svc->completed_count }}
                                        </span>
                                    </td>
                                    <td class="font-black text-slate-900 font-mono">
                                        {{ $svc->total_today }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Right: Bed Occupancy & Quick Forensic Audit Feed --}}
            <div class="space-y-6">
                {{-- Bed Capacity Card --}}
                <div class="card p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-black text-slate-900">Ward & Bed Capacity</h2>
                        <a href="{{ route('staff.beds.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">View Bays &rarr;</a>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-semibold text-slate-700">Occupancy Rate:</span>
                            <span class="font-black text-purple-700">{{ $bedStats['occupancy_rate'] }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-3 rounded-full transition-all duration-500" style="width: {{ min(100, $bedStats['occupancy_rate']) }}%"></div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-center pt-2 text-xs">
                            <div class="p-2 bg-emerald-50 rounded-lg text-emerald-800">
                                <span class="font-black text-sm block">{{ $bedStats['available'] }}</span>
                                <span class="text-[10px] text-emerald-600">Available</span>
                            </div>
                            <div class="p-2 bg-purple-50 rounded-lg text-purple-800">
                                <span class="font-black text-sm block">{{ $bedStats['occupied'] }}</span>
                                <span class="text-[10px] text-purple-600">Occupied</span>
                            </div>
                            <div class="p-2 bg-slate-100 rounded-lg text-slate-800">
                                <span class="font-black text-sm block">{{ $bedStats['maintenance'] }}</span>
                                <span class="text-[10px] text-slate-500">Cleaning</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recent Audit Feed --}}
                <div class="card p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-black text-slate-900">Recent Forensic Audit Trail</h2>
                        <a href="{{ route('admin.audit.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">Full Log &rarr;</a>
                    </div>

                    <div class="space-y-3 divide-y divide-slate-100">
                        @foreach($recentAudit as $log)
                            <div class="pt-2 text-xs flex items-start justify-between gap-2">
                                <div>
                                    <span class="font-bold text-slate-800 block">{{ $log->action }}</span>
                                    <span class="text-[11px] text-slate-500">{{ $log->user?->name ?? 'System' }} &bull; {{ $log->ip_address }}</span>
                                </div>
                                <span class="text-[10px] font-mono text-slate-400 whitespace-nowrap">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Hourly Patient Inflow Line Chart
            const hourlyCtx = document.getElementById('hourlyTrendChart');
            if (hourlyCtx) {
                new Chart(hourlyCtx, {
                    type: 'line',
                    data: {
                        labels: @json($hourlyLabels),
                        datasets: [{
                            label: 'Patient Arrivals',
                            data: @json($hourlyData),
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#4f46e5',
                            pointRadius: 4,
                            pointHoverRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0, font: { size: 10 } },
                                grid: { color: '#f1f5f9' }
                            },
                            x: {
                                ticks: { font: { size: 10 } },
                                grid: { display: false }
                            }
                        }
                    }
                });
            }

            // 2. Triage Severity Doughnut Chart
            const triageCtx = document.getElementById('triageDoughnutChart');
            if (triageCtx) {
                new Chart(triageCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Red (P1)', 'Orange (P2)', 'Yellow (P3)', 'Green (P4)', 'Blue (P5)'],
                        datasets: [{
                            data: [
                                {{ $triageCounts['RED'] }},
                                {{ $triageCounts['ORANGE'] }},
                                {{ $triageCounts['YELLOW'] }},
                                {{ $triageCounts['GREEN'] }},
                                {{ $triageCounts['BLUE'] }}
                            ],
                            backgroundColor: [
                                '#dc2626', // Red
                                '#ea580c', // Orange
                                '#eab308', // Yellow
                                '#16a34a', // Green
                                '#2563eb'  // Blue
                            ],
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
                        cutout: '65%'
                    }
                });
            }
        });
    </script>
    @endpush
</x-layouts.app>
