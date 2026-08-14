<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\QueueEntry;
use App\Models\Service;
use App\Services\QueueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class QueueController extends Controller
{
    public function __construct(private readonly QueueService $queueService) {}

    /**
     * Show available services to join a queue.
     */
    public function index(): View
    {
        $services = Service::active()
            ->orderBy('name')
            ->get()
            ->map(function ($service) {
                // Add live queue stats to each service
                $service->waiting_count = $service->waitingCount;
                $service->currently_serving = $service->lastCalled?->queue_number;
                return $service;
            });

        return view('patient.services', compact('services'));
    }

    /**
     * Show queue join confirmation for a specific service.
     */
    public function show(Service $service): View
    {
        abort_unless($service->is_active, 404, 'This service is not currently available.');

        $waitingCount = QueueEntry::where('service_id', $service->id)
            ->whereDate('created_at', today())
            ->where('status', QueueEntry::STATUS_WAITING)
            ->count();

        $estimatedWait = $waitingCount * $service->avg_duration_minutes;

        $currentlyServing = QueueEntry::where('service_id', $service->id)
            ->whereDate('created_at', today())
            ->whereIn('status', [QueueEntry::STATUS_CALLED, QueueEntry::STATUS_IN_SERVICE])
            ->latest('called_at')
            ->value('queue_number');

        // Check if patient already has an active entry for this service
        $existingEntry = QueueEntry::where('patient_id', Auth::id())
            ->where('service_id', $service->id)
            ->whereIn('status', QueueEntry::ACTIVE_STATUSES)
            ->whereDate('created_at', today())
            ->first();

        return view('patient.join-queue', compact(
            'service',
            'waitingCount',
            'estimatedWait',
            'currentlyServing',
            'existingEntry'
        ));
    }

    /**
     * Join the queue for a service.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
        ]);

        $service = Service::findOrFail($request->service_id);

        $entry = $this->queueService->join(Auth::user(), $service);

        return redirect()->route('patient.queue.status', $entry)
            ->with('success', "You have joined the queue! Your number is {$entry->queue_number}.");
    }

    /**
     * Show live status for a specific queue entry.
     */
    public function status(QueueEntry $queueEntry): View
    {
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
     * Cancel a waiting queue entry.
     */
    public function cancel(QueueEntry $queueEntry): RedirectResponse
    {
        abort_unless($queueEntry->patient_id === Auth::id(), 403);

        $this->queueService->cancel($queueEntry, Auth::user());

        return redirect()->route('patient.dashboard')
            ->with('success', "Your queue entry {$queueEntry->queue_number} has been cancelled.");
    }

    /**
     * JSON endpoint for live queue status polling.
     * Returns current position data without a full page reload.
     */
    public function statusJson(QueueEntry $queueEntry): \Illuminate\Http\JsonResponse
    {
        abort_unless($queueEntry->patient_id === Auth::id(), 403);

        $queueEntry->refresh()->load('service');

        $positionData  = $this->queueService->getPosition($queueEntry);
        $estimatedWait = $this->queueService->getEstimatedWaitMinutes($queueEntry);

        return response()->json([
            'status'          => $queueEntry->status,
            'status_label'    => $queueEntry->status_label,
            'queue_number'    => $queueEntry->queue_number,
            'position'        => $positionData['position'],
            'people_ahead'    => $positionData['people_ahead'],
            'currently_serving' => $positionData['currently_serving'],
            'estimated_wait'  => $estimatedWait,
        ]);
    }
}
