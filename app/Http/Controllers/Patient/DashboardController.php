<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\QueueEntry;
use App\Models\Service;
use App\Services\QueueService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly QueueService $queueService) {}

    /**
     * Patient home dashboard.
     * Shows active queue entry (if any), recent history, and available services.
     */
    public function index(): View
    {
        $user = Auth::user();

        // Current active queue entries for this patient (today)
        $activeEntries = QueueEntry::where('patient_id', $user->id)
            ->whereDate('created_at', today())
            ->whereIn('status', QueueEntry::ACTIVE_STATUSES)
            ->with('service')
            ->orderBy('created_at', 'desc')
            ->get();

        // Enrich entries with position data
        $enrichedEntries = $activeEntries->map(function ($entry) {
            $positionData = $this->queueService->getPosition($entry);
            $estimatedWait = $this->queueService->getEstimatedWaitMinutes($entry);
            return [
                'entry'         => $entry,
                'position'      => $positionData['position'],
                'people_ahead'  => $positionData['people_ahead'],
                'serving'       => $positionData['currently_serving'],
                'estimated_wait'=> $estimatedWait,
            ];
        });

        // Recent queue history
        $history = QueueEntry::where('patient_id', $user->id)
            ->with('service')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Active services for quick join
        $services = Service::active()->orderBy('name')->get();

        // Unread notifications
        $notifications = $user->appNotifications()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $unreadCount = $user->unreadNotificationsCount();

        return view('patient.dashboard', compact(
            'enrichedEntries',
            'history',
            'services',
            'notifications',
            'unreadCount'
        ));
    }

    /**
     * View a specific queue entry with live position data.
     */
    public function showEntry(QueueEntry $queueEntry): View
    {
        // Authorization: patient can only view their own entries
        abort_unless($queueEntry->patient_id === Auth::id(), 403);

        $queueEntry->load('service', 'patient');

        $positionData  = $this->queueService->getPosition($queueEntry);
        $estimatedWait = $this->queueService->getEstimatedWaitMinutes($queueEntry);

        return view('patient.queue-status', compact(
            'queueEntry',
            'positionData',
            'estimatedWait'
        ));
    }

    /**
     * Show queue history for the patient.
     */
    public function history(Request $request): View
    {
        $history = QueueEntry::where('patient_id', Auth::id())
            ->with('service')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('patient.history', compact('history'));
    }

    /**
     * Mark all notifications as read.
     */
    public function markNotificationsRead(): RedirectResponse
    {
        Auth::user()->appNotifications()->whereNull('read_at')->update(['read_at' => now()]);
        return back()->with('success', 'All notifications marked as read.');
    }
}
