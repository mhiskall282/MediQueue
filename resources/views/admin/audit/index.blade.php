<x-layouts.app title="Immutable Audit Trail">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200 mb-2">
                Security & Governance
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">System Audit Log</h1>
            <p class="text-slate-500 text-sm mt-1">Immutable, time-stamped activity records for compliance and accountability.</p>
        </div>

        {{-- Filter Bar --}}
        <div class="card p-4 mb-6">
            <form method="GET" action="{{ route('admin.audit.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input
                        type="text"
                        name="action"
                        value="{{ request('action') }}"
                        placeholder="Search by action name (e.g. queue.called, service.created)..."
                        class="form-input text-sm"
                    >
                </div>
                <div class="w-full sm:w-56">
                    <select name="entity_type" onchange="this.form.submit()" class="form-input text-sm">
                        <option value="">All Entities</option>
                        <option value="QueueEntry" {{ request('entity_type') === 'QueueEntry' ? 'selected' : '' }}>QueueEntry</option>
                        <option value="Service" {{ request('entity_type') === 'Service' ? 'selected' : '' }}>Service</option>
                        <option value="User" {{ request('entity_type') === 'User' ? 'selected' : '' }}>User</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-secondary text-sm">Filter</button>
            </form>
        </div>

        <div class="card overflow-hidden">
            @if($logs->isEmpty())
                <div class="p-12 text-center text-slate-400 text-sm">
                    No audit log records match the selected filters.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Actor</th>
                                <th>Action Event</th>
                                <th>Target Entity</th>
                                <th>Metadata Context</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td class="font-mono text-xs text-slate-500">
                                        {{ $log->created_at->format('Y-m-d H:i:s') }}
                                    </td>
                                    <td>
                                        @if($log->user)
                                            <p class="font-bold text-slate-900 text-xs">{{ $log->user->name }}</p>
                                            <p class="text-[10px] text-slate-400">{{ $log->user->role }}</p>
                                        @else
                                            <span class="text-xs text-slate-400 italic">System Event</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="font-mono text-xs font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td class="text-xs font-semibold text-slate-700">
                                        {{ $log->entity_type }} #{{ $log->entity_id ?? 'N/A' }}
                                    </td>
                                    <td class="text-xs font-mono text-slate-600 max-w-xs truncate">
                                        {{ json_encode($log->metadata) }}
                                    </td>
                                    <td class="text-xs font-mono text-slate-400">
                                        {{ $log->ip_address ?? '127.0.0.1' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $logs->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.app>
