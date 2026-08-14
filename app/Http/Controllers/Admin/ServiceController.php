<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * List all services.
     */
    public function index(): View
    {
        $services = Service::withCount(['queueEntries as total_today' => function ($q) {
            $q->whereDate('created_at', today());
        }])->orderBy('name')->paginate(20);

        return view('admin.services.index', compact('services'));
    }

    /**
     * Show the create service form.
     */
    public function create(): View
    {
        return view('admin.services.create');
    }

    /**
     * Store a new service.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'                 => ['required', 'string', 'max:100'],
            'description'          => ['nullable', 'string', 'max:500'],
            'prefix'               => ['required', 'string', 'max:10', 'alpha', 'unique:services,prefix'],
            'avg_duration_minutes' => ['required', 'integer', 'min:1', 'max:120'],
        ]);

        $data['prefix']    = strtoupper($data['prefix']);
        $data['is_active'] = true;

        $service = Service::create($data);

        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'service.created',
            'entity_type' => 'Service',
            'entity_id'   => $service->id,
            'metadata'    => ['name' => $service->name, 'prefix' => $service->prefix],
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('admin.services.index')
            ->with('success', "Service \"{$service->name}\" created successfully.");
    }

    /**
     * Show the edit service form.
     */
    public function edit(Service $service): View
    {
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Update an existing service.
     */
    public function update(Request $request, Service $service): RedirectResponse
    {
        $data = $request->validate([
            'name'                 => ['required', 'string', 'max:100'],
            'description'          => ['nullable', 'string', 'max:500'],
            'prefix'               => ['required', 'string', 'max:10', 'alpha', "unique:services,prefix,{$service->id}"],
            'avg_duration_minutes' => ['required', 'integer', 'min:1', 'max:120'],
        ]);

        $data['prefix'] = strtoupper($data['prefix']);

        $old = $service->only('name', 'prefix', 'avg_duration_minutes');
        $service->update($data);

        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'service.updated',
            'entity_type' => 'Service',
            'entity_id'   => $service->id,
            'metadata'    => ['old' => $old, 'new' => $data],
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('admin.services.index')
            ->with('success', "Service \"{$service->name}\" updated successfully.");
    }

    /**
     * Toggle a service active/inactive.
     * Note: We never delete services to preserve historical queue records.
     */
    public function toggle(Request $request, Service $service): RedirectResponse
    {
        $service->update(['is_active' => !$service->is_active]);

        $action = $service->is_active ? 'service.activated' : 'service.deactivated';
        $label  = $service->is_active ? 'activated' : 'deactivated';

        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'entity_type' => 'Service',
            'entity_id'   => $service->id,
            'metadata'    => ['name' => $service->name, 'is_active' => $service->is_active],
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('admin.services.index')
            ->with('success', "Service \"{$service->name}\" {$label}.");
    }
}
