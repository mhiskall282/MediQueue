<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Bed;
use App\Models\Notification;
use App\Models\QueueEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TriageController extends Controller
{
    /**
     * Display live hospital beds & triage capacity management.
     */
    public function bedsIndex(Request $request): View
    {
        $beds = Bed::with(['currentPatient'])->orderBy('ward_name')->orderBy('bed_number')->get();

        $totalBeds     = $beds->count();
        $occupiedBeds  = $beds->where('status', Bed::STATUS_OCCUPIED)->count();
        $availableBeds = $beds->where('status', Bed::STATUS_AVAILABLE)->count();

        // Active queue entries that can be assigned a bed
        $activeEntries = QueueEntry::with(['patient', 'service'])
            ->whereIn('status', [QueueEntry::STATUS_WAITING, QueueEntry::STATUS_CALLED, QueueEntry::STATUS_IN_SERVICE])
            ->whereNull('allocated_bed_id')
            ->orderBy('joined_at')
            ->get();

        return view('staff.beds.index', compact('beds', 'totalBeds', 'occupiedBeds', 'availableBeds', 'activeEntries'));
    }

    /**
     * Update clinical triage level and priority classification.
     */
    public function updateTriage(Request $request, QueueEntry $queueEntry): RedirectResponse
    {
        $validated = $request->validate([
            'triage_level' => ['required', 'in:RED,ORANGE,YELLOW,GREEN,BLUE'],
            'triage_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldLevel = $queueEntry->triage_level;
        $newLevel = $validated['triage_level'];

        $queueEntry->update([
            'triage_level' => $newLevel,
            'triage_notes' => $validated['triage_notes'] ?? $queueEntry->triage_notes,
            // If RED or ORANGE, automatically escalate priority to URGENT
            'priority'     => in_array($newLevel, ['RED', 'ORANGE']) ? 'URGENT' : $queueEntry->priority,
        ]);

        // Audit Trail
        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'triage.updated',
            'entity_type' => 'QueueEntry',
            'entity_id'   => $queueEntry->id,
            'metadata'    => [
                'queue_number' => $queueEntry->queue_number,
                'old_triage'   => $oldLevel,
                'new_triage'   => $newLevel,
                'notes'        => $validated['triage_notes'] ?? null,
                'staff_name'   => auth()->user()->name,
            ],
            'ip_address'  => $request->ip(),
        ]);

        // Patient In-App Alert
        Notification::create([
            'user_id' => $queueEntry->patient_id,
            'type'    => 'triage_assessed',
            'title'   => 'Clinical Triage Assessed',
            'body'    => "Your clinical priority has been evaluated by medical staff as {$queueEntry->triage_label}.",
            'data'    => ['queue_id' => $queueEntry->id, 'triage' => $newLevel],
        ]);

        return back()->with('success', "Ticket {$queueEntry->queue_number} triage level updated to {$queueEntry->triage_label}.");
    }

    /**
     * Allocate a hospital bed / bay to a queue patient.
     */
    public function allocateBed(Request $request, QueueEntry $queueEntry): RedirectResponse
    {
        $validated = $request->validate([
            'bed_id' => ['required', 'exists:beds,id'],
        ]);

        $bed = Bed::findOrFail($validated['bed_id']);

        if (!$bed->isAvailable()) {
            return back()->with('error', "Bed {$bed->bed_number} is already occupied or unavailable.");
        }

        // Release previous bed if patient was already in one
        if ($queueEntry->allocated_bed_id) {
            $prevBed = Bed::find($queueEntry->allocated_bed_id);
            if ($prevBed) {
                $prevBed->update(['status' => Bed::STATUS_AVAILABLE, 'current_patient_id' => null]);
            }
        }

        // Occupy new bed
        $bed->update([
            'status'             => Bed::STATUS_OCCUPIED,
            'current_patient_id' => $queueEntry->patient_id,
        ]);

        $queueEntry->update([
            'allocated_bed_id' => $bed->id,
        ]);

        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'bed.allocated',
            'entity_type' => 'Bed',
            'entity_id'   => $bed->id,
            'metadata'    => [
                'bed_number'   => $bed->bed_number,
                'ward'         => $bed->ward_name,
                'queue_number' => $queueEntry->queue_number,
                'patient_name' => $queueEntry->patient->name ?? 'Patient',
            ],
            'ip_address'  => $request->ip(),
        ]);

        return back()->with('success', "Patient {$queueEntry->queue_number} allocated to Bed {$bed->bed_number} ({$bed->ward_name}).");
    }

    /**
     * Release an allocated bed back into the available pool.
     */
    public function releaseBed(Request $request, QueueEntry $queueEntry): RedirectResponse
    {
        if ($queueEntry->allocated_bed_id) {
            $bed = Bed::find($queueEntry->allocated_bed_id);
            if ($bed) {
                $bed->update([
                    'status'             => Bed::STATUS_AVAILABLE,
                    'current_patient_id' => null,
                ]);

                AuditLog::create([
                    'user_id'     => auth()->id(),
                    'action'      => 'bed.released',
                    'entity_type' => 'Bed',
                    'entity_id'   => $bed->id,
                    'metadata'    => [
                        'bed_number'   => $bed->bed_number,
                        'queue_number' => $queueEntry->queue_number,
                    ],
                    'ip_address'  => $request->ip(),
                ]);
            }

            $queueEntry->update(['allocated_bed_id' => null]);
        }

        return back()->with('success', "Bed successfully released to available status.");
    }
}
