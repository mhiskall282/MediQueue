<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SecurityAlert;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SecurityAlertController extends Controller
{
    /**
     * Display real-time security alerts and HIPAA / ISO 27001 telemetry.
     */
    public function index(Request $request): View
    {
        $query = SecurityAlert::with(['user', 'resolver']);

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('status')) {
            if ($request->status === 'unresolved') {
                $query->where('is_resolved', false);
            } elseif ($request->status === 'resolved') {
                $query->where('is_resolved', true);
            }
        }

        $criticalCount   = SecurityAlert::where('severity', SecurityAlert::SEVERITY_CRITICAL)->where('is_resolved', false)->count();
        $unresolvedCount = SecurityAlert::where('is_resolved', false)->count();
        $totalCount      = SecurityAlert::count();

        $alerts = $query->orderByDesc('created_at')->paginate(25)->withQueryString();

        return view('admin.security.index', compact('alerts', 'criticalCount', 'unresolvedCount', 'totalCount'));
    }

    /**
     * Mark a security alert as resolved with mitigation audit notes.
     */
    public function resolve(Request $request, SecurityAlert $alert): RedirectResponse
    {
        $alert->update([
            'is_resolved' => true,
            'resolved_at' => now(),
            'resolved_by' => Auth::id(),
        ]);

        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'security_alert.resolved',
            'entity_type' => 'SecurityAlert',
            'entity_id'   => $alert->id,
            'metadata'    => [
                'event_type' => $alert->event_type,
                'severity'   => $alert->severity,
            ],
            'ip_address'  => $request->ip(),
        ]);

        return back()->with('success', "Security alert #{$alert->id} marked as resolved and logged in compliance audit.");
    }
}
