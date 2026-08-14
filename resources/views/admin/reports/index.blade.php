<x-layouts.app title="Clinic Reports & Operational Analytics">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Clinic Reports & Analytics</h1>
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
                    <button type="submit" class="btn btn-secondary text-xs sm:text-sm">
                        <svg class="w-4 h-4 text-indigo-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Email Summary to Admin
                    </button>
                </form>

                {{-- Export CSV Button --}}
                <a href="{{ route('admin.reports.export', request()->all()) }}" class="btn btn-primary text-xs sm:text-sm shadow-md">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export to CSV
                </a>
            </div>
        </div>

        {{-- Filter Card --}}
        <div class="card p-6 mb-8">
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
                                {{ $staff->name }} ({{ $staff->role_label }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="btn btn-primary w-full text-xs">
                        Filter Records
                    </button>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary text-xs">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Metric Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <div class="stat-card">
                <span class="text-xs font-bold text-slate-500 uppercase block">Total Volume</span>
                <span class="text-2xl font-black text-slate-900 mt-1 block">{{ number_format($totalEntries) }}</span>
                <span class="text-[11px] text-slate-400">Patients registered</span>
            </div>

            <div class="stat-card">
                <span class="text-xs font-bold text-emerald-600 uppercase block">Completed</span>
                <span class="text-2xl font-black text-emerald-600 mt-1 block">{{ number_format($completedEntries) }}</span>
                <span class="text-[11px] text-slate-400">{{ $totalEntries > 0 ? round(($completedEntries / $totalEntries) * 100) : 0 }}% completion rate</span>
            </div>

            <div class="stat-card">
                <span class="text-xs font-bold text-orange-600 uppercase block">Skipped No-Shows</span>
                <span class="text-2xl font-black text-orange-600 mt-1 block">{{ number_format($skippedEntries) }}</span>
                <span class="text-[11px] text-slate-400">Unanswered callouts</span>
            </div>

            <div class="stat-card">
                <span class="text-xs font-bold text-indigo-600 uppercase block">Avg Wait Duration</span>
                <span class="text-2xl font-black text-indigo-600 mt-1 block">{{ round($avgWaitMinutes) }} min</span>
                <span class="text-[11px] text-slate-400">From join to call</span>
            </div>

            <div class="stat-card">
                <span class="text-xs font-bold text-violet-600 uppercase block">Avg Consultation</span>
                <span class="text-2xl font-black text-violet-600 mt-1 block">{{ round($avgServiceMinutes) }} min</span>
                <span class="text-[11px] text-slate-400">Time spent with doctor</span>
            </div>
        </div>

        {{-- Records Table --}}
        <div class="card overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">
                    Clinical Activity Log ({{ $entries->total() }} total entries)
                </span>
            </div>

            @if($entries->isEmpty())
                <div class="p-12 text-center text-slate-500">
                    <p class="font-medium text-sm">No queue records match your filter criteria.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Ticket #</th>
                                <th>Patient</th>
                                <th>Department</th>
                                <th>Attending Doctor / Staff</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Wait Time</th>
                                <th>Consult Time</th>
                                <th class="text-right">Chain of Custody</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entries as $entry)
                                <tr>
                                    <td>
                                        <span class="font-mono font-bold text-xs px-2 py-1 bg-slate-100 rounded text-slate-900">
                                            {{ $entry->queue_number }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="font-semibold text-slate-900">{{ $entry->patient->name ?? 'Deleted User' }}</div>
                                        <div class="text-xs text-slate-400">{{ $entry->patient->email ?? 'N/A' }}</div>
                                    </td>
                                    <td>{{ $entry->service->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($entry->servedBy)
                                            <div class="font-medium text-slate-800">{{ $entry->servedBy->name }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $entry->servedBy->role_label }}</div>
                                        @else
                                            <span class="text-xs text-slate-400 italic">Unassigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $entry->status_badge_class }}">
                                            {{ $entry->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-xs text-slate-500">
                                        {{ $entry->joined_at ? $entry->joined_at->format('M d, H:i') : '-' }}
                                    </td>
                                    <td class="text-xs font-medium text-slate-700">
                                        {{ $entry->wait_duration_minutes !== null ? $entry->wait_duration_minutes . ' min' : '-' }}
                                    </td>
                                    <td class="text-xs font-medium text-slate-700">
                                        {{ $entry->service_duration_minutes !== null ? $entry->service_duration_minutes . ' min' : '-' }}
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('admin.reports.investigate', $entry) }}" class="btn btn-secondary btn-sm text-xs text-indigo-600 font-bold hover:text-indigo-800">
                                            🔍 Investigate
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100">
                    {{ $entries->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
