<x-layouts.app title="Clinical Investigation — Ticket {{ $queueEntry->queue_number }}">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumb & Header --}}
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-4">
            <a href="{{ route('admin.reports.index') }}" class="hover:text-indigo-600">Reports</a>
            <span>/</span>
            <span class="text-slate-900">Forensic Investigation</span>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-tr from-indigo-700 to-indigo-500 text-white rounded-2xl flex items-center justify-center font-black text-xl shadow-md">
                    🔍
                </div>
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                        Chain of Custody & Clinical Audit
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Forensic tracking of attending staff, consultation milestones, and audit ledger for Ticket <strong>{{ $queueEntry->queue_number }}</strong>.
                    </p>
                </div>
            </div>

            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary text-xs">
                &larr; Back to Reports
            </a>
        </div>

        {{-- Ticket & Patient Summary Card --}}
        <div class="card p-6 mb-8 border-l-4 border-indigo-600">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Ticket Number</span>
                    <span class="text-2xl font-black text-indigo-600 font-mono">{{ $queueEntry->queue_number }}</span>
                    <span class="badge {{ $queueEntry->status_badge_class }} mt-2">{{ $queueEntry->status_label }}</span>
                </div>

                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Patient Details</span>
                    <p class="font-bold text-slate-900">{{ $queueEntry->patient->name ?? 'Unknown' }}</p>
                    <p class="text-xs text-slate-500">{{ $queueEntry->patient->email ?? 'N/A' }}</p>
                    <p class="text-xs text-slate-400 mt-1">ID: #{{ $queueEntry->patient_id }} &bull; Phone: {{ $queueEntry->patient->phone ?? 'N/A' }}</p>
                </div>

                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Clinic Department</span>
                    <p class="font-bold text-slate-900">{{ $queueEntry->service->name ?? 'N/A' }}</p>
                    <p class="text-xs text-slate-500">Prefix: <code>{{ $queueEntry->service->prefix ?? 'N/A' }}</code></p>
                    <p class="text-xs text-slate-400 mt-1">Priority: <span class="uppercase font-semibold">{{ $queueEntry->priority }}</span></p>
                </div>

                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Attending Clinician</span>
                    @if($queueEntry->servedBy)
                        <p class="font-bold text-slate-900">{{ $queueEntry->servedBy->name }}</p>
                        <p class="text-xs text-indigo-600 font-semibold">{{ $queueEntry->servedBy->role_label }}</p>
                        <p class="text-xs text-slate-400 mt-1">Staff ID: #{{ $queueEntry->served_by }}</p>
                    @else
                        <p class="text-xs text-slate-400 italic">No clinician assigned / pending</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Forensic Investigation Timeline --}}
        <div class="card p-6 sm:p-8 mb-8">
            <h2 class="text-lg font-black text-slate-900 mb-6 flex items-center gap-2">
                <span>⏱️</span> Operational Lifecycle Timeline
            </h2>

            <div class="relative border-l-2 border-slate-200 ml-4 space-y-8 pl-6">
                {{-- 1. Ticket Joined --}}
                <div class="relative">
                    <span class="absolute -left-[31px] top-0 w-4 h-4 rounded-full bg-emerald-500 ring-4 ring-white"></span>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-900 text-sm">1. Ticket Issued & Registered</span>
                            <span class="text-xs text-slate-400">
                                {{ $queueEntry->joined_at ? $queueEntry->joined_at->format('M d, Y H:i:s') : 'N/A' }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 mt-1">
                            Registered by patient <strong>{{ $queueEntry->patient->name ?? 'Patient' }}</strong> (ID: #{{ $queueEntry->patient_id }}) for department <strong>{{ $queueEntry->service->name }}</strong>.
                        </p>
                    </div>
                </div>

                {{-- 2. Ticket Called --}}
                <div class="relative">
                    <span class="absolute -left-[31px] top-0 w-4 h-4 rounded-full {{ $queueEntry->called_at ? 'bg-indigo-600' : 'bg-slate-300' }} ring-4 ring-white"></span>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-900 text-sm">2. Patient Called to Consultation Desk</span>
                            <span class="text-xs text-slate-400">
                                {{ $queueEntry->called_at ? $queueEntry->called_at->format('M d, Y H:i:s') : 'Not yet called' }}
                            </span>
                        </div>
                        @if($queueEntry->called_at)
                            <p class="text-xs text-slate-600 mt-1">
                                Called by staff member <strong>{{ $queueEntry->servedBy->name ?? 'Staff' }}</strong> (ID: #{{ $queueEntry->served_by }}). Wait duration was <strong>{{ $queueEntry->wait_duration_minutes }} minutes</strong>. Public TV screen and transactional email notifications were dispatched.
                            </p>
                        @else
                            <p class="text-xs text-slate-400 italic mt-1">Patient is currently waiting in queue.</p>
                        @endif
                    </div>
                </div>

                {{-- 3. Service Started --}}
                <div class="relative">
                    <span class="absolute -left-[31px] top-0 w-4 h-4 rounded-full {{ $queueEntry->service_started_at ? 'bg-emerald-600' : 'bg-slate-300' }} ring-4 ring-white"></span>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-900 text-sm">3. Consultation Started</span>
                            <span class="text-xs text-slate-400">
                                {{ $queueEntry->service_started_at ? $queueEntry->service_started_at->format('M d, Y H:i:s') : 'Pending patient arrival' }}
                            </span>
                        </div>
                        @if($queueEntry->service_started_at)
                            <p class="text-xs text-slate-600 mt-1">
                                Patient arrived in the room. Clinician <strong>{{ $queueEntry->servedBy->name ?? 'Staff' }}</strong> initiated consultation.
                            </p>
                        @endif
                    </div>
                </div>

                {{-- 4. Terminal State (Completed / Skipped / Cancelled) --}}
                <div class="relative">
                    <span class="absolute -left-[31px] top-0 w-4 h-4 rounded-full {{ $queueEntry->isTerminal() ? 'bg-slate-800' : 'bg-slate-300' }} ring-4 ring-white"></span>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-900 text-sm">4. Lifecycle Concluded ({{ $queueEntry->status_label }})</span>
                            <span class="text-xs text-slate-400">
                                {{ $queueEntry->completed_at ? $queueEntry->completed_at->format('M d, Y H:i:s') : ($queueEntry->cancelled_at ? $queueEntry->cancelled_at->format('M d, Y H:i:s') : 'In progress') }}
                            </span>
                        </div>
                        @if($queueEntry->status === 'COMPLETED')
                            <p class="text-xs text-slate-600 mt-1">
                                Consultation successfully completed by <strong>{{ $queueEntry->servedBy->name ?? 'Staff' }}</strong>. Total doctor consultation time: <strong>{{ $queueEntry->service_duration_minutes }} minutes</strong>.
                            </p>
                        @elseif($queueEntry->status === 'SKIPPED')
                            <p class="text-xs text-orange-600 mt-1">
                                Patient marked as no-show / skipped by <strong>{{ $queueEntry->servedBy->name ?? 'Staff' }}</strong>.
                            </p>
                        @elseif($queueEntry->status === 'CANCELLED')
                            <p class="text-xs text-rose-600 mt-1">
                                Ticket was cancelled before consultation began.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Forensic Audit Log Ledger --}}
        <div class="card overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600">
                    Immutable Audit Ledger Entries ({{ $auditLogs->count() }} records)
                </h3>
            </div>

            @if($auditLogs->isEmpty())
                <div class="p-8 text-center text-xs text-slate-400">
                    No individual audit records matched this ticket ID.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="data-table text-xs">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Action</th>
                                <th>Actor (Who Performed)</th>
                                <th>Client IP Address</th>
                                <th>Metadata Context</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($auditLogs as $log)
                                <tr>
                                    <td class="font-mono text-slate-500">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>
                                        <span class="font-mono font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($log->user)
                                            <span class="font-semibold text-slate-900">{{ $log->user->name }}</span>
                                            <span class="text-[10px] text-slate-400 block">{{ $log->user->role_label }} (#{{ $log->user_id }})</span>
                                        @else
                                            <span class="text-slate-400 italic">System Event</span>
                                        @endif
                                    </td>
                                    <td class="font-mono text-slate-500">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                                    <td class="font-mono text-[11px] text-slate-600">
                                        @if($log->metadata)
                                            {{ json_encode($log->metadata) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
