<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\QueueEntry;
use App\Models\Service;
use App\Services\QueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class QueueController extends Controller
{
    public function __construct(private readonly QueueService $queueService) {}

    /**
     * Staff dashboard — shows queue for a selected service.
     */
    public function dashboard(Request $request): View
    {
        $services = Service::active()->orderBy('name')->get();

        // Default to first available service, or staff can choose
        $selectedServiceId = $request->input('service_id', $services->first()?->id);
        $selectedService   = $services->find($selectedServiceId);

        $waitingEntries = collect();
        $calledEntries  = collect();
        $completedToday = 0;
        $skippedToday   = 0;
        $avgWaitMinutes = 0;

        if ($selectedService) {
            $waitingEntries = QueueEntry::where('service_id', $selectedService->id)
                ->whereDate('created_at', today())
                ->where('status', QueueEntry::STATUS_WAITING)
                ->with('patient')
                ->byQueueOrder()
                ->get();

            $calledEntries = QueueEntry::where('service_id', $selectedService->id)
                ->whereDate('created_at', today())
                ->whereIn('status', [QueueEntry::STATUS_CALLED, QueueEntry::STATUS_IN_SERVICE])
                ->with('patient', 'staffMember')
                ->orderBy('called_at', 'desc')
                ->get();

            $completedToday = QueueEntry::where('service_id', $selectedService->id)
                ->whereDate('created_at', today())
                ->where('status', QueueEntry::STATUS_COMPLETED)
                ->count();

            $skippedToday = QueueEntry::where('service_id', $selectedService->id)
                ->whereDate('created_at', today())
                ->where('status', QueueEntry::STATUS_SKIPPED)
                ->count();

            // Average wait time for completed entries today (minutes)
            $completedEntries = QueueEntry::where('service_id', $selectedService->id)
                ->whereDate('created_at', today())
                ->where('status', QueueEntry::STATUS_COMPLETED)
                ->whereNotNull('service_started_at')
                ->get();

            if ($completedEntries->count() > 0) {
                $totalWait = $completedEntries->sum(function ($e) {
                    return $e->joined_at->diffInMinutes($e->service_started_at);
                });
                $avgWaitMinutes = round($totalWait / $completedEntries->count());
            }
        }

        return view('staff.dashboard', compact(
            'services',
            'selectedService',
            'waitingEntries',
            'calledEntries',
            'completedToday',
            'skippedToday',
            'avgWaitMinutes'
        ));
    }

    /**
     * Call the next patient in the queue.
     */
    public function callNext(Request $request): RedirectResponse
    {
        $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
        ]);

        $service = Service::findOrFail($request->service_id);
        $this->queueService->callNext($service, Auth::user());

        return redirect()->route('staff.dashboard', ['service_id' => $service->id])
            ->with('success', 'Next patient called.');
    }

    /**
     * Start service for a called patient.
     */
    public function startService(QueueEntry $queueEntry): RedirectResponse
    {
        $this->queueService->startService($queueEntry, Auth::user());

        return redirect()->route('staff.dashboard', ['service_id' => $queueEntry->service_id])
            ->with('success', "Service started for {$queueEntry->queue_number}.");
    }

    /**
     * Complete service for an in-service patient.
     */
    public function complete(QueueEntry $queueEntry): RedirectResponse
    {
        $this->queueService->complete($queueEntry, Auth::user());

        return redirect()->route('staff.dashboard', ['service_id' => $queueEntry->service_id])
            ->with('success', "Service completed for {$queueEntry->queue_number}.");
    }

    /**
     * Skip a called patient.
     */
    public function skip(QueueEntry $queueEntry): RedirectResponse
    {
        $this->queueService->skip($queueEntry, Auth::user());

        return redirect()->route('staff.dashboard', ['service_id' => $queueEntry->service_id])
            ->with('success', "{$queueEntry->queue_number} skipped.");
    }

    /**
     * Recall a skipped patient.
     */
    public function recall(QueueEntry $queueEntry): RedirectResponse
    {
        $this->queueService->recall($queueEntry, Auth::user());

        return redirect()->route('staff.dashboard', ['service_id' => $queueEntry->service_id])
            ->with('success', "{$queueEntry->queue_number} recalled.");
    }

    /**
     * JSON endpoint for live queue status on the staff dashboard.
     */
    public function liveStatus(Request $request): JsonResponse
    {
        $serviceId = $request->integer('service_id');

        $waiting = QueueEntry::where('service_id', $serviceId)
            ->whereDate('created_at', today())
            ->where('status', QueueEntry::STATUS_WAITING)
            ->count();

        $currentlyServing = QueueEntry::where('service_id', $serviceId)
            ->whereDate('created_at', today())
            ->whereIn('status', [QueueEntry::STATUS_CALLED, QueueEntry::STATUS_IN_SERVICE])
            ->with('patient')
            ->latest('called_at')
            ->first();

        return response()->json([
            'waiting_count'    => $waiting,
            'currently_serving'=> $currentlyServing ? [
                'queue_number' => $currentlyServing->queue_number,
                'patient_name' => $currentlyServing->patient->name,
                'status'       => $currentlyServing->status,
            ] : null,
        ]);
    }
}
