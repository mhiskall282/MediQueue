<?php

namespace App\Http\Controllers;

use App\Models\QueueEntry;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DisplayController extends Controller
{
    /**
     * Public Hospital / Clinic Waiting Room Display Screen.
     */
    public function index(): View
    {
        $clinicName = Setting::get('clinic_name', 'MediQueue Central Clinic');

        $services = Service::active()->orderBy('name')->get();

        // Currently called & in service entries across all departments
        $activeCalled = QueueEntry::whereDate('created_at', today())
            ->whereIn('status', [QueueEntry::STATUS_CALLED, QueueEntry::STATUS_IN_SERVICE])
            ->with(['service', 'patient'])
            ->orderByDesc('called_at')
            ->limit(6)
            ->get();

        // Top called entry for huge headline display
        $leadCalled = $activeCalled->first();

        return view('display', compact('clinicName', 'services', 'activeCalled', 'leadCalled'));
    }

    /**
     * JSON polling endpoint for the public TV display board.
     */
    public function data(): JsonResponse
    {
        $activeCalled = QueueEntry::whereDate('created_at', today())
            ->whereIn('status', [QueueEntry::STATUS_CALLED, QueueEntry::STATUS_IN_SERVICE])
            ->with('service')
            ->orderByDesc('called_at')
            ->limit(6)
            ->get()
            ->map(function ($entry) {
                return [
                    'queue_number' => $entry->queue_number,
                    'service_name' => $entry->service->name,
                    'prefix'       => $entry->service->prefix,
                    'status'       => $entry->status,
                    'called_time'  => $entry->called_at?->format('g:i A'),
                ];
            });

        $departments = Service::active()->orderBy('name')->get()->map(function ($service) {
            $current = QueueEntry::where('service_id', $service->id)
                ->whereDate('created_at', today())
                ->whereIn('status', [QueueEntry::STATUS_CALLED, QueueEntry::STATUS_IN_SERVICE])
                ->latest('called_at')
                ->first();

            $waitingCount = QueueEntry::where('service_id', $service->id)
                ->whereDate('created_at', today())
                ->where('status', QueueEntry::STATUS_WAITING)
                ->count();

            return [
                'id'           => $service->id,
                'name'         => $service->name,
                'prefix'       => $service->prefix,
                'current'      => $current?->queue_number ?? '—',
                'waitingCount' => $waitingCount,
            ];
        });

        return response()->json([
            'called'      => $activeCalled,
            'departments' => $departments,
            'time'        => now()->format('g:i:s A'),
            'date'        => now()->format('l, F j, Y'),
        ]);
    }
}
