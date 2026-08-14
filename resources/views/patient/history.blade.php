<x-layouts.app title="Queue History">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Your Queue History</h1>
            <p class="text-slate-500 text-sm mt-1">Review all your past and current clinic queue tickets.</p>
        </div>

        <div class="card overflow-hidden">
            @if($history->isEmpty())
                <div class="p-12 text-center text-slate-500 text-sm">
                    You have no past queue entries.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Ticket #</th>
                                <th>Service Department</th>
                                <th>Joined At</th>
                                <th>Status</th>
                                <th>Wait Duration</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($history as $entry)
                                <tr>
                                    <td class="font-bold text-indigo-700 font-mono">{{ $entry->queue_number }}</td>
                                    <td class="font-medium text-slate-900">{{ $entry->service->name }}</td>
                                    <td>{{ $entry->joined_at->format('M d, Y - g:i A') }}</td>
                                    <td>
                                        <span class="badge {{ $entry->status_badge_class }}">
                                            {{ $entry->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $entry->wait_duration_minutes !== null ? $entry->wait_duration_minutes . ' mins' : '—' }}
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('patient.queue.status', $entry) }}" class="btn btn-secondary btn-sm">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($history->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $history->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.app>
