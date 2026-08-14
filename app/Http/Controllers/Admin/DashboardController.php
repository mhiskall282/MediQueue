<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Bed;
use App\Models\QueueEntry;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Admin system dashboard with real-time statistics and visual analytics.
     */
    public function index(): View
    {
        $today = Carbon::today();

        $stats = [
            'total_patients'    => User::where('role', 'patient')->count(),
            'total_staff'       => User::where('role', 'staff')->count(),
            'active_on_call'    => User::where('role', 'staff')->where('is_on_call', true)->count(),
            'active_services'   => Service::where('is_active', true)->count(),
            'waiting_now'       => QueueEntry::whereDate('created_at', $today)->where('status', 'WAITING')->count(),
            'in_service_now'    => QueueEntry::whereDate('created_at', $today)->whereIn('status', ['CALLED', 'IN_SERVICE'])->count(),
            'completed_today'   => QueueEntry::whereDate('created_at', $today)->where('status', 'COMPLETED')->count(),
            'cancelled_today'   => QueueEntry::whereDate('created_at', $today)->where('status', 'CANCELLED')->count(),
            'skipped_today'     => QueueEntry::whereDate('created_at', $today)->where('status', 'SKIPPED')->count(),
            'total_today'       => QueueEntry::whereDate('created_at', $today)->count(),
        ];

        // Average wait time today (completed entries only)
        $avgWait = QueueEntry::whereDate('created_at', $today)
            ->where('status', 'COMPLETED')
            ->whereNotNull('service_started_at')
            ->get()
            ->avg(fn ($e) => $e->joined_at->diffInMinutes($e->service_started_at));

        $stats['avg_wait_minutes'] = $avgWait ? round($avgWait) : 0;

        // 1. Triage Severity Distribution (Pie / Doughnut Chart)
        $triageCounts = [
            'RED'    => QueueEntry::whereDate('created_at', $today)->where('triage_level', 'RED')->count(),
            'ORANGE' => QueueEntry::whereDate('created_at', $today)->where('triage_level', 'ORANGE')->count(),
            'YELLOW' => QueueEntry::whereDate('created_at', $today)->where('triage_level', 'YELLOW')->count(),
            'GREEN'  => QueueEntry::whereDate('created_at', $today)->where('triage_level', 'GREEN')->count(),
            'BLUE'   => QueueEntry::whereDate('created_at', $today)->where('triage_level', 'BLUE')->count(),
        ];

        // 2. Hourly Patient Arrival Distribution (08:00 - 18:00) (Line Chart)
        $hourlyLabels = ['8 AM', '9 AM', '10 AM', '11 AM', '12 PM', '1 PM', '2 PM', '3 PM', '4 PM', '5 PM', '6 PM'];
        $hourlyData   = [];
        for ($hour = 8; $hour <= 18; $hour++) {
            $hourlyData[] = QueueEntry::whereDate('created_at', $today)
                ->whereTime('created_at', '>=', sprintf('%02d:00:00', $hour))
                ->whereTime('created_at', '<=', sprintf('%02d:59:59', $hour))
                ->count();
        }

        // 3. Hospital Beds Occupancy Overview
        $bedStats = [
            'total'       => Bed::count(),
            'occupied'    => Bed::where('status', Bed::STATUS_OCCUPIED)->count(),
            'available'   => Bed::where('status', Bed::STATUS_AVAILABLE)->count(),
            'maintenance' => Bed::where('status', Bed::STATUS_MAINTENANCE)->count(),
        ];
        $bedStats['occupancy_rate'] = $bedStats['total'] > 0 ? round(($bedStats['occupied'] / $bedStats['total']) * 100) : 0;

        // 4. Per-service breakdown
        $serviceBreakdown = Service::withCount([
            'queueEntries as waiting_count' => fn ($q) => $q->whereDate('created_at', $today)->where('status', 'WAITING'),
            'queueEntries as completed_count' => fn ($q) => $q->whereDate('created_at', $today)->where('status', 'COMPLETED'),
            'queueEntries as total_today' => fn ($q) => $q->whereDate('created_at', $today),
        ])->orderByDesc('total_today')->get();

        // 5. Recent audit logs
        $recentAudit = AuditLog::with('user')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'triageCounts',
            'hourlyLabels',
            'hourlyData',
            'bedStats',
            'serviceBreakdown',
            'recentAudit'
        ));
    }
}
