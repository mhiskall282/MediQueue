<x-layouts.app title="System Administration Dashboard">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200 mb-2">
                    System Control Center
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Admin Overview</h1>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.services.create') }}" class="btn btn-primary btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Service
                </a>
            </div>
        </div>

        {{-- Top KPI Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">
            <div class="stat-card">
                <span class="text-xs font-bold uppercase text-slate-500 block">Total Patients</span>
                <span class="text-3xl font-black text-slate-900 mt-2 block">{{ $stats['total_patients'] }}</span>
            </div>
            <div class="stat-card">
                <span class="text-xs font-bold uppercase text-slate-500 block">Clinical Staff</span>
                <span class="text-3xl font-black text-slate-900 mt-2 block">{{ $stats['total_staff'] }}</span>
            </div>
            <div class="stat-card">
                <span class="text-xs font-bold uppercase text-indigo-600 block">Waiting Right Now</span>
                <span class="text-3xl font-black text-indigo-700 mt-2 block">{{ $stats['waiting_now'] }}</span>
            </div>
            <div class="stat-card">
                <span class="text-xs font-bold uppercase text-emerald-600 block">Completed Today</span>
                <span class="text-3xl font-black text-emerald-700 mt-2 block">{{ $stats['completed_today'] }}</span>
            </div>
        </div>

        {{-- Department Performance Breakdown --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <div class="lg:col-span-2 card p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-slate-900">Clinic Department Performance (Today)</h2>
                    <a href="{{ route('admin.services.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Manage Services &rarr;</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Department</th>
                                <th>Prefix</th>
                                <th>Avg Est.</th>
                                <th>Waiting</th>
                                <th>Completed</th>
                                <th>Total Tickets</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($serviceBreakdown as $svc)
                                <tr>
                                    <td class="font-bold text-slate-900">{{ $svc->name }}</td>
                                    <td><span class="font-mono text-xs font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded">{{ $svc->prefix }}</span></td>
                                    <td class="text-xs">{{ $svc->avg_duration_minutes }}m</td>
                                    <td class="font-bold text-amber-600">{{ $svc->waiting_count }}</td>
                                    <td class="font-bold text-emerald-600">{{ $svc->completed_count }}</td>
                                    <td class="font-bold text-slate-900">{{ $svc->total_today }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Today's Realtime Status Summary --}}
            <div class="card p-6">
                <h2 class="text-lg font-bold text-slate-900 mb-6">System Health & Metrics</h2>
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Active Services</span>
                        <span class="font-bold text-slate-800">{{ $stats['active_services'] }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">In Service Now</span>
                        <span class="font-bold text-indigo-600">{{ $stats['in_service_now'] }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Average Wait (Today)</span>
                        <span class="font-bold text-slate-800">~{{ $stats['avg_wait_minutes'] }} mins</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Skipped Entries</span>
                        <span class="font-bold text-amber-600">{{ $stats['skipped_today'] }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-slate-500">Total Volume Today</span>
                        <span class="font-bold text-slate-900">{{ $stats['total_today'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Audit Activity --}}
        <div class="card p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Recent Immutable Audit Trail</h2>
                    <p class="text-xs text-slate-500">Real-time system events logged for security and accountability</p>
                </div>
                <a href="{{ route('admin.audit.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Full Audit Log &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Actor</th>
                            <th>Action</th>
                            <th>Entity</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentAudit as $log)
                            <tr>
                                <td class="text-xs font-mono text-slate-500">{{ $log->created_at->format('g:i:s A') }}</td>
                                <td class="font-semibold text-slate-800 text-xs">{{ $log->user?->name ?? 'System' }}</td>
                                <td><span class="font-mono text-xs text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded">{{ $log->action }}</span></td>
                                <td class="text-xs text-slate-600">{{ $log->entity_type }} #{{ $log->entity_id }}</td>
                                <td class="text-xs font-mono text-slate-400">{{ $log->ip_address ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
