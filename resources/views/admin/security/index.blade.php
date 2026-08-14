<x-layouts.app title="Security Alerts & Compliance Telemetry (HIPAA / ISO 27001)">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-black tracking-tight text-slate-900">HIPAA & ISO 27001 Security Center</h1>
                    <span class="badge bg-red-100 text-red-800 font-black text-xs border border-red-200">
                        AUDIT ENFORCED
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Continuous anomaly monitoring, brute-force tracking, unauthorized access attempts, and medical compliance telemetry.
                </p>
            </div>
        </div>

        {{-- Telemetry KPI Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="stat-card border-l-4 border-red-600">
                <span class="text-xs font-bold text-red-600 uppercase block">Critical Vulnerabilities</span>
                <span class="text-3xl font-black text-red-600 mt-1 block">{{ $criticalCount }}</span>
                <span class="text-[11px] text-slate-400">Immediate action required</span>
            </div>

            <div class="stat-card border-l-4 border-amber-500">
                <span class="text-xs font-bold text-amber-600 uppercase block">Unresolved Incidents</span>
                <span class="text-3xl font-black text-amber-600 mt-1 block">{{ $unresolvedCount }}</span>
                <span class="text-[11px] text-slate-400">Pending review & mitigation</span>
            </div>

            <div class="stat-card border-l-4 border-indigo-500">
                <span class="text-xs font-bold text-indigo-600 uppercase block">Total Security Logs</span>
                <span class="text-3xl font-black text-indigo-600 mt-1 block">{{ $totalCount }}</span>
                <span class="text-[11px] text-slate-400">Recorded telemetry events</span>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="card p-4">
            <form method="GET" action="{{ route('admin.security.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="form-label text-xs">Severity Level</label>
                    <select name="severity" class="form-input text-xs" onchange="this.form.submit()">
                        <option value="">All Severities</option>
                        <option value="CRITICAL" {{ request('severity') === 'CRITICAL' ? 'selected' : '' }}>🔴 Critical</option>
                        <option value="HIGH" {{ request('severity') === 'HIGH' ? 'selected' : '' }}>🟠 High</option>
                        <option value="MEDIUM" {{ request('severity') === 'MEDIUM' ? 'selected' : '' }}>🟡 Medium</option>
                        <option value="LOW" {{ request('severity') === 'LOW' ? 'selected' : '' }}>🔵 Low / Informational</option>
                    </select>
                </div>

                <div>
                    <label class="form-label text-xs">Resolution Status</label>
                    <select name="status" class="form-input text-xs" onchange="this.form.submit()">
                        <option value="">All Incidents</option>
                        <option value="unresolved" {{ request('status') === 'unresolved' ? 'selected' : '' }}>⚠️ Unresolved Only</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>✅ Resolved</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <a href="{{ route('admin.security.index') }}" class="btn btn-secondary text-xs w-full justify-center">
                        Reset Filters
                    </a>
                </div>
            </form>
        </div>

        {{-- Incident Table --}}
        <div class="card overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-600">
                    Security Incident & Telemetry Stream ({{ $alerts->total() }} events)
                </span>
            </div>

            @if($alerts->isEmpty())
                <div class="p-12 text-center text-slate-500 text-sm">
                    <span class="text-3xl block mb-2">🛡️</span>
                    No security alerts or compliance anomalies detected. System is secure.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Severity</th>
                                <th>Event Type</th>
                                <th>Description / Context</th>
                                <th>IP Address</th>
                                <th>Timestamp</th>
                                <th>Status</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($alerts as $alert)
                                <tr class="{{ !$alert->is_resolved ? 'bg-red-50/30' : '' }}">
                                    <td>
                                        <span class="badge {{ $alert->severity_badge_class }} text-[10px] uppercase">
                                            {{ $alert->severity }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="font-mono text-xs font-bold text-slate-800">
                                            {{ $alert->event_type }}
                                        </span>
                                    </td>
                                    <td class="max-w-md">
                                        <p class="text-xs text-slate-800 font-medium">{{ $alert->description }}</p>
                                        @if($alert->user)
                                            <span class="text-[11px] text-slate-500 block mt-0.5">
                                                Actor: <strong>{{ $alert->user->name }}</strong> ({{ $alert->user->role_title }})
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="font-mono text-[11px] text-slate-500">{{ $alert->ip_address ?? '—' }}</span>
                                    </td>
                                    <td>
                                        <span class="text-[11px] text-slate-500">{{ $alert->created_at->format('M d, H:i:s') }}</span>
                                    </td>
                                    <td>
                                        @if($alert->is_resolved)
                                            <span class="badge bg-emerald-100 text-emerald-800 text-[10px]">
                                                ✅ Resolved by {{ $alert->resolver?->name ?? 'Admin' }}
                                            </span>
                                        @else
                                            <span class="badge bg-amber-100 text-amber-800 text-[10px] animate-pulse">
                                                ⚠️ Open Incident
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if(!$alert->is_resolved)
                                            <form method="POST" action="{{ route('admin.security.resolve', $alert) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="btn btn-secondary btn-xs text-[10px] font-bold text-emerald-700 hover:bg-emerald-50">
                                                    Mark Resolved
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[11px] text-slate-400">Audited</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($alerts->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $alerts->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.app>
