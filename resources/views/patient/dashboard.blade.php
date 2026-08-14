<x-layouts.app title="Patient Dashboard">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Welcome Banner --}}
        <div class="bg-gradient-to-r from-indigo-900 to-indigo-700 rounded-2xl p-6 sm:p-8 text-white mb-8 shadow-sm relative overflow-hidden">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Welcome back, {{ auth()->user()->name }}</h1>
                    <p class="text-indigo-200 mt-1 text-sm sm:text-base">Check your live queue status or select a service to join a new queue.</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('patient.queue.index') }}" class="btn btn-primary bg-white text-indigo-700 hover:bg-indigo-50 shadow-md border-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Join New Queue
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Column (Active Tickets & Quick Services) --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Active Queue Tickets --}}
                <div class="card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Active Queue Status (Today)
                        </h2>
                        <span class="text-xs text-slate-500">Auto-refreshing</span>
                    </div>

                    @if($enrichedEntries->isEmpty())
                        <div class="text-center py-10 border-2 border-dashed border-slate-200 rounded-xl">
                            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-sm font-semibold text-slate-800">You are not currently in any queue</h3>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Choose an available clinic service below to take a digital queue ticket.</p>
                            <div class="mt-4">
                                <a href="{{ route('patient.queue.index') }}" class="btn btn-primary btn-sm">Browse Services</a>
                            </div>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($enrichedEntries as $item)
                                @php $entry = $item['entry']; @endphp
                                <div class="border border-slate-200 rounded-xl p-5 bg-slate-50 hover:bg-white transition-colors duration-150 relative">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <div class="flex items-start gap-4">
                                            <div class="text-center bg-indigo-600 text-white rounded-xl p-3 min-w-[90px] shadow-sm">
                                                <span class="text-xs uppercase font-medium tracking-wider text-indigo-200 block">Ticket</span>
                                                <span class="text-2xl font-black tracking-tight block">{{ $entry->queue_number }}</span>
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <h3 class="font-bold text-slate-900">{{ $entry->service->name }}</h3>
                                                    <span class="badge {{ $entry->status_badge_class }}">
                                                        {{ $entry->status_label }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-slate-500 mt-1">Joined at {{ $entry->joined_at->format('g:i A') }}</p>

                                                @if($entry->status === 'WAITING')
                                                    <div class="flex flex-wrap items-center gap-4 mt-3 text-xs text-slate-600">
                                                        <span class="inline-flex items-center gap-1 font-semibold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded">
                                                            Position: #{{ $item['position'] }}
                                                        </span>
                                                        <span>People ahead: <strong>{{ $item['people_ahead'] }}</strong></span>
                                                        <span>Est. Wait: <strong>~{{ $item['estimated_wait'] }} mins</strong></span>
                                                    </div>
                                                @elseif($entry->status === 'CALLED')
                                                    <div class="mt-3 p-2.5 bg-indigo-50 border border-indigo-200 rounded-lg text-xs text-indigo-900 font-semibold flex items-center gap-2">
                                                        <span class="w-2 h-2 rounded-full bg-indigo-600 animate-ping"></span>
                                                        You are called! Please proceed immediately to the consultation counter.
                                                    </div>
                                                @elseif($entry->status === 'IN_SERVICE')
                                                    <div class="mt-3 p-2.5 bg-emerald-50 border border-emerald-200 rounded-lg text-xs text-emerald-900 font-semibold flex items-center gap-2">
                                                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                                        Currently in service with clinical staff.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2 sm:self-center">
                                            <a href="{{ route('patient.queue.status', $entry) }}" class="btn btn-secondary btn-sm">
                                                Live Status
                                            </a>
                                            @if($entry->status === 'WAITING')
                                                <form method="POST" action="{{ route('patient.queue.cancel', $entry) }}" onsubmit="return confirm('Are you sure you want to cancel your queue ticket?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50">
                                                        Cancel
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Available Services Catalogue --}}
                <div class="card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Clinic Services</h2>
                            <p class="text-xs text-slate-500">Select a service to take a queue ticket</p>
                        </div>
                        <a href="{{ route('patient.queue.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">View All &rarr;</a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($services as $service)
                            <div class="border border-slate-200 rounded-xl p-4 hover:border-indigo-300 hover:bg-indigo-50/30 transition-all duration-150 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded uppercase">{{ $service->prefix }}</span>
                                        <span class="text-xs text-slate-500">~{{ $service->avg_duration_minutes }} mins/patient</span>
                                    </div>
                                    <h3 class="font-bold text-slate-900 mt-2 text-sm">{{ $service->name }}</h3>
                                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $service->description }}</p>
                                </div>
                                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                    <span class="text-xs text-slate-600">Waiting: <strong>{{ $service->waitingCount }}</strong></span>
                                    <a href="{{ route('patient.queue.show', $service) }}" class="btn btn-primary btn-sm">Join</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Sidebar Column (Notifications & Quick Profile) --}}
            <div class="space-y-8">
                {{-- Notifications Box --}}
                <div class="card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <span>Notifications</span>
                            @if($unreadCount > 0)
                                <span class="bg-indigo-100 text-indigo-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $unreadCount }}</span>
                            @endif
                        </h2>
                        @if($unreadCount > 0)
                            <form method="POST" action="{{ route('patient.notifications.read') }}">
                                @csrf
                                <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">Mark read</button>
                            </form>
                        @endif
                    </div>

                    @if($notifications->isEmpty())
                        <p class="text-xs text-slate-400 text-center py-6">No notifications yet.</p>
                    @else
                        <div class="divide-y divide-slate-100">
                            @foreach($notifications as $notif)
                                <div class="py-3 {{ $notif->isUnread() ? 'bg-indigo-50/40 -mx-2 px-2 rounded-lg' : '' }}">
                                    <p class="text-xs font-semibold text-slate-800">{{ $notif->title }}</p>
                                    <p class="text-xs text-slate-600 mt-0.5">{{ $notif->body }}</p>
                                    <span class="text-[10px] text-slate-400 mt-1 block">{{ $notif->created_at->diffForHumans() }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Recent History Summary --}}
                <div class="card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base font-bold text-slate-900">Recent Visits</h2>
                        <a href="{{ route('patient.history') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Full History &rarr;</a>
                    </div>

                    @if($history->isEmpty())
                        <p class="text-xs text-slate-400 text-center py-6">No previous queue activity.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($history->take(5) as $h)
                                <div class="flex items-center justify-between text-xs py-2 border-b border-slate-100 last:border-0">
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $h->queue_number }} ({{ $h->service->name }})</p>
                                        <p class="text-slate-400 text-[11px]">{{ $h->created_at->format('M d, Y') }}</p>
                                    </div>
                                    <span class="badge {{ $h->status_badge_class }}">{{ $h->status_label }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
