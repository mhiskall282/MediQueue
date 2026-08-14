<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\QueueEntry;
use App\Models\Service;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Admin system dashboard with real-time statistics.
     */
    public function index(): View
    {
        $stats = [
            'total_patients'    => User::where('role', 'patient')->count(),
            'total_staff'       => User::where('role', 'staff')->count(),
            'active_services'   => Service::where('is_active', true)->count(),
            'waiting_now'       => QueueEntry::whereDate('created_at', today())
                                       ->where('status', 'WAITING')->count(),
            'in_service_now'    => QueueEntry::whereDate('created_at', today())
                                       ->whereIn('status', ['CALLED', 'IN_SERVICE'])->count(),
            'completed_today'   => QueueEntry::whereDate('created_at', today())
                                       ->where('status', 'COMPLETED')->count(),
            'cancelled_today'   => QueueEntry::whereDate('created_at', today())
                                       ->where('status', 'CANCELLED')->count(),
            'skipped_today'     => QueueEntry::whereDate('created_at', today())
                                       ->where('status', 'SKIPPED')->count(),
            'total_today'       => QueueEntry::whereDate('created_at', today())->count(),
        ];

        // Average wait time today (completed entries only)
        $avgWait = QueueEntry::whereDate('created_at', today())
            ->where('status', 'COMPLETED')
            ->whereNotNull('service_started_at')
            ->get()
            ->avg(fn ($e) => $e->joined_at->diffInMinutes($e->service_started_at));

        $stats['avg_wait_minutes'] = $avgWait ? round($avgWait) : 0;

        // Per-service breakdown
        $serviceBreakdown = Service::withCount([
            'queueEntries as waiting_count' => fn ($q) => $q->whereDate('created_at', today())->where('status', 'WAITING'),
            'queueEntries as completed_count' => fn ($q) => $q->whereDate('created_at', today())->where('status', 'COMPLETED'),
            'queueEntries as total_today' => fn ($q) => $q->whereDate('created_at', today()),
        ])->orderByDesc('total_today')->get();

        // Recent audit logs
        $recentAudit = AuditLog::with('user')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'serviceBreakdown', 'recentAudit'));
    }
}
