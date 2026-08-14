<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Mail\QueueNotificationMail;
use App\Models\AuditLog;
use App\Models\Bed;
use App\Models\Notification;
use App\Models\QueueEntry;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClinicalReferralController extends Controller
{
    /**
     * Doctor transfers patient to Diagnostic Lab / Radiology with test orders.
     */
    public function orderLabAndTransfer(Request $request, QueueEntry $queueEntry): RedirectResponse
    {
        $validated = $request->validate([
            'clinical_notes' => ['required', 'string'],
            'lab_orders'     => ['required', 'string'],
        ]);

        $doctor = auth()->user();

        // Find or create Laboratory Service
        $labService = Service::where('prefix', 'LAB')->orWhere('name', 'like', '%Lab%')->first();
        if (!$labService) {
            $labService = Service::create([
                'name'                 => 'Clinical Diagnostic Laboratory',
                'prefix'               => 'LAB',
                'avg_duration_minutes' => 15,
                'is_active'            => true,
            ]);
        }

        $queueEntry->update([
            'service_id'              => $labService->id,
            'referring_staff_id'      => $doctor->id,
            'served_by'               => null,
            'status'                  => QueueEntry::STATUS_WAITING,
            'clinical_workflow_stage' => QueueEntry::STAGE_SENT_TO_LAB,
            'clinical_notes'          => $validated['clinical_notes'],
            'lab_orders'              => $validated['lab_orders'],
        ]);

        AuditLog::create([
            'user_id'     => $doctor->id,
            'action'      => 'referral.sent_to_lab',
            'entity_type' => 'QueueEntry',
            'entity_id'   => $queueEntry->id,
            'metadata'    => [
                'queue_number' => $queueEntry->queue_number,
                'doctor_name'  => $doctor->name,
                'lab_orders'   => $validated['lab_orders'],
            ],
            'ip_address'  => $request->ip(),
        ]);

        Notification::create([
            'user_id' => $queueEntry->patient_id,
            'type'    => 'lab_referral',
            'title'   => 'Referred to Diagnostic Laboratory',
            'body'    => "Dr. {$doctor->name} has ordered diagnostic investigations: {$validated['lab_orders']}. Please report to the Laboratory queue.",
            'data'    => ['queue_id' => $queueEntry->id],
        ]);

        return redirect()->route('staff.dashboard')->with('success', "Patient {$queueEntry->queue_number} successfully referred to Diagnostic Lab.");
    }

    /**
     * Lab technician completes tests and automatically loops ticket back to referring doctor for clinical review.
     */
    public function completeLabAndReturn(Request $request, QueueEntry $queueEntry): RedirectResponse
    {
        $validated = $request->validate([
            'lab_results' => ['required', 'string'],
        ]);

        $referringDoctor = $queueEntry->referringStaff;

        // Return ticket to referring doctor's department (or General Consultation) with Urgent priority
        $returnServiceId = $referringDoctor?->service_id ?? 1;

        $queueEntry->update([
            'service_id'              => $returnServiceId,
            'status'                  => QueueEntry::STATUS_WAITING,
            'triage_level'            => QueueEntry::TRIAGE_ORANGE, // Priority retention for doctor review!
            'priority'                => 'URGENT',
            'clinical_workflow_stage' => QueueEntry::STAGE_RETURNED_FOR_REVIEW,
            'lab_results'             => $validated['lab_results'],
            'served_by'               => null,
        ]);

        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'referral.lab_completed',
            'entity_type' => 'QueueEntry',
            'entity_id'   => $queueEntry->id,
            'metadata'    => [
                'queue_number' => $queueEntry->queue_number,
                'lab_staff'    => auth()->user()->name,
                'results'      => $validated['lab_results'],
            ],
            'ip_address'  => $request->ip(),
        ]);

        // Priority notification to referring doctor
        if ($referringDoctor) {
            Notification::create([
                'user_id' => $referringDoctor->id,
                'type'    => 'lab_results_ready',
                'title'   => "Lab Findings Ready: Ticket {$queueEntry->queue_number}",
                'body'    => "Diagnostic investigations for Patient {$queueEntry->patient->name} (MRN: {$queueEntry->hospital_id}) are completed and returned for your clinical review.",
                'data'    => ['queue_id' => $queueEntry->id],
            ]);
        }

        return redirect()->route('staff.dashboard')->with('success', "Lab findings recorded! Ticket {$queueEntry->queue_number} returned to Doctor for clinical review with priority.");
    }

    /**
     * Doctor concludes consultation, issues discharge summary and releases bed.
     */
    public function discharge(Request $request, QueueEntry $queueEntry): RedirectResponse
    {
        $validated = $request->validate([
            'discharge_summary' => ['required', 'string'],
            'prescriptions'     => ['nullable', 'string'],
        ]);

        $doctor = auth()->user();

        // Release bed if occupied
        if ($queueEntry->allocated_bed_id) {
            $bed = Bed::find($queueEntry->allocated_bed_id);
            if ($bed) {
                $bed->update(['status' => Bed::STATUS_AVAILABLE, 'current_patient_id' => null]);
            }
        }

        $fullNotes = trim($queueEntry->clinical_notes . "\n\nDischarge Summary: " . $validated['discharge_summary'] . ($validated['prescriptions'] ? "\nPrescriptions: " . $validated['prescriptions'] : ''));

        $queueEntry->update([
            'status'                  => QueueEntry::STATUS_COMPLETED,
            'clinical_workflow_stage' => QueueEntry::STAGE_DISCHARGED,
            'completed_at'            => now(),
            'clinical_notes'          => $fullNotes,
            'allocated_bed_id'        => null,
            'served_by'               => $doctor->id,
        ]);

        AuditLog::create([
            'user_id'     => $doctor->id,
            'action'      => 'patient.discharged',
            'entity_type' => 'QueueEntry',
            'entity_id'   => $queueEntry->id,
            'metadata'    => [
                'queue_number' => $queueEntry->queue_number,
                'doctor_name'  => $doctor->name,
                'summary'      => $validated['discharge_summary'],
            ],
            'ip_address'  => $request->ip(),
        ]);

        // Send Discharge Summary Email
        try {
            Mail::to($queueEntry->patient->email)->send(new QueueNotificationMail(
                $queueEntry->patient,
                'Medical Consultation & Discharge Summary',
                'Consultation Concluded',
                "Your clinical consultation with Dr. {$doctor->name} is completed. Below is your clinical summary and care recommendations:\n\n{$validated['discharge_summary']}",
                [
                    'Attending Doctor' => $doctor->name,
                    'MRN'              => $queueEntry->hospital_id ?? 'N/A',
                    'Prescriptions'    => $validated['prescriptions'] ?? 'None',
                    'Discharged At'    => now()->format('M d, Y H:i'),
                ]
            ));
        } catch (\Throwable $e) {
            // Silently continue
        }

        return redirect()->route('staff.dashboard')->with('success', "Patient {$queueEntry->queue_number} discharged and consultation summary dispatched.");
    }
}
