<x-layouts.app title="Service Catalogue Management">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Clinic Services Catalogue</h1>
                <p class="text-slate-500 text-sm mt-1">Configure service departments, ticket prefixes, and estimated consultation durations.</p>
            </div>
            <a href="{{ route('admin.services.create') }}" class="btn btn-primary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add New Service
            </a>
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Department Name</th>
                            <th>Prefix</th>
                            <th>Avg Duration</th>
                            <th>Status</th>
                            <th>Today's Volume</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($services as $service)
                            <tr>
                                <td>
                                    <p class="font-bold text-slate-900">{{ $service->name }}</p>
                                    <p class="text-xs text-slate-500 max-w-sm line-clamp-1">{{ $service->description }}</p>
                                </td>
                                <td>
                                    <span class="font-mono text-xs font-bold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded">
                                        {{ $service->prefix }}
                                    </span>
                                </td>
                                <td class="text-sm text-slate-700">
                                    {{ $service->avg_duration_minutes }} minutes
                                </td>
                                <td>
                                    @if($service->is_active)
                                        <span class="badge badge-in-service">Active</span>
                                    @else
                                        <span class="badge badge-completed">Inactive</span>
                                    @endif
                                </td>
                                <td class="font-bold text-slate-800">
                                    {{ $service->total_today }} tickets
                                </td>
                                <td class="text-right space-x-2">
                                    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-secondary btn-sm">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.services.toggle', $service) }}" class="inline-block" onsubmit="return confirm('Change status for this service?');">
                                        @csrf
                                        <button type="submit" class="btn btn-ghost btn-sm {{ $service->is_active ? 'text-amber-700' : 'text-emerald-700' }}">
                                            {{ $service->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($services->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $services->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
