<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Bed;
use App\Models\Notification;
use App\Models\QueueEntry;
use App\Models\Service;
use App\Models\User;
use App\Services\QueueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EmergencyIntakeController extends Controller
{
    public function __construct(
        protected QueueService $queueService
    ) {}

    /**
     * Show Emergency Trauma & Rapid Intake Portal.
     */
    public function index(): View
    {
        $emergencyEntries = QueueEntry::with(['patient', 'service', 'allocatedBed'])
            ->where('is_emergency_unconscious', true)
            ->whereIn('status', [QueueEntry::STATUS_WAITING, QueueEntry::STATUS_CALLED, QueueEntry::STATUS_IN_SERVICE])
            ->orderByDesc('joined_at')
            ->get();

        $triageBays = Bed::where('bed_type', Bed::TYPE_TRIAGE_BAY)->get();
        $onCallDoctors = User::where('role', 'staff')->where('is_on_call', true)->get();

        return view('staff.emergency.index', compact('emergencyEntries', 'triageBays', 'onCallDoctors'));
    }

    /**
     * Execute Emergency Rapid Intake Protocol for Unconscious / Unidentified Trauma Patients.
     */
    public function unconsciousIntake(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'estimated_gender'    => ['required', 'in:MALE,FEMALE,UNKNOWN'],
            'intake_notes'        => ['required', 'string'],
            'vital_signs'         => ['nullable', 'string'],
            'allocated_bay_id'    => ['nullable', 'exists:beds,id'],
        ]);

        $randomSuffix = rand(1000, 9999);
        $patientName  = sprintf('Unidentified %s Doe #%d', ucfirst(strtolower($validated['estimated_gender'])), $randomSuffix);
        $emergencyEmail = sprintf('trauma.%s.%d@hospital.emergency', strtolower($validated['estimated_gender']), $randomSuffix);
        $emergencyMrn   = sprintf('EMG-DOE-%d', $randomSuffix);

        // Create temporary trauma emergency patient
        $emergencyPatient = User::create([
            'hospital_id' => $emergencyMrn,
            'name'        => $patientName,
            'email'       => $emergencyEmail,
            'password'    => Hash::make(Str::random(32)),
            'role'        => 'patient',
            'phone'       => 'EMERGENCY-UNCONSCIOUS',
        ]);

        // Find or create Emergency Service
        $emergencyService = Service::where('prefix', 'EMG')->orWhere('name', 'like', '%Emergency%')->first();
        if (!$emergencyService) {
            $emergencyService = Service::create([
                'name'                 => 'Emergency & Trauma Resuscitation',
                'prefix'               => 'EMG',
                'avg_duration_minutes' => 30,
                'is_active'            => true,
            ]);
        }

        // Issue atomic emergency ticket via QueueService
        $queueEntry = $this->queueService->join(
            $emergencyPatient,
            $emergencyService,
            'URGENT'
        );

        // Update emergency flags, RED triage, and notes
        $queueEntry->update([
            'hospital_id'              => $emergencyMrn,
            'is_emergency_unconscious' => true,
            'triage_level'             => QueueEntry::TRIAGE_RED, // P1 Immediate Resuscitation!
            'priority'                 => 'URGENT',
            'triage_notes'             => "EMERGENCY PROTOCOL (UNCONSCIOUS INTAKE):\n" . $validated['intake_notes'] . ($validated['vital_signs'] ? "\nVitals: " . $validated['vital_signs'] : ''),
        ]);

        // Auto-allocate bed/bay if selected or find first available triage bay
        $bayId = $validated['allocated_bay_id'] ?? Bed::where('bed_type', Bed::TYPE_TRIAGE_BAY)->where('status', Bed::STATUS_AVAILABLE)->first()?->id;
        if ($bayId) {
            $bay = Bed::find($bayId);
            if ($bay && $bay->isAvailable()) {
                $bay->update([
                    'status'             => Bed::STATUS_OCCUPIED,
                    'current_patient_id' => $emergencyPatient->id,
                ]);
                $queueEntry->update(['allocated_bed_id' => $bay->id]);
            }
        }

        // Audit Trail
        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'emergency.unconscious_intake',
            'entity_type' => 'QueueEntry',
            'entity_id'   => $queueEntry->id,
            'metadata'    => [
                'mrn'          => $emergencyMrn,
                'queue_number' => $queueEntry->queue_number,
                'staff_name'   => auth()->user()->name,
                'bay_allocated'=> $bay->bed_number ?? 'Pending',
            ],
            'ip_address'  => $request->ip(),
        ]);

        // Page all active on-call doctors with urgent alert!
        $onCallDoctors = User::where('role', 'staff')->where('is_on_call', true)->get();
        foreach ($onCallDoctors as $doc) {
            Notification::create([
                'user_id' => $doc->id,
                'type'    => 'code_red_emergency',
                'title'   => "🚨 CODE RED: Emergency Trauma Patient {$queueEntry->queue_number}",
                'body'    => "Unconscious patient admitted ({$emergencyMrn}). Allocated to " . ($bay->bed_number ?? 'Triage Bay') . ". Immediate medical attendance requested!",
                'data'    => ['queue_id' => $queueEntry->id, 'mrn' => $emergencyMrn],
            ]);
        }

        return redirect()->route('staff.emergency.index')->with('success', "🚨 CODE RED: Emergency intake registered for {$patientName} ({$emergencyMrn}). On-call doctors paged!");
    }

    /**
     * Link emergency trauma ticket to a patient's verified permanent medical record.
     */
    public function linkPermanentId(Request $request, QueueEntry $queueEntry): RedirectResponse
    {
        $validated = $request->validate([
            'permanent_user_id' => ['required', 'exists:users,id'],
            'verified_mrn'      => ['required', 'string'],
        ]);

        $permanentPatient = User::findOrFail($validated['permanent_user_id']);

        $queueEntry->update([
            'patient_id'               => $permanentPatient->id,
            'hospital_id'              => $validated['verified_mrn'],
            'is_emergency_unconscious' => false,
        ]);

        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'emergency.identity_linked',
            'entity_type' => 'QueueEntry',
            'entity_id'   => $queueEntry->id,
            'metadata'    => [
                'queue_number'   => $queueEntry->queue_number,
                'verified_name'  => $permanentPatient->name,
                'verified_mrn'   => $validated['verified_mrn'],
                'staff_name'     => auth()->user()->name,
            ],
            'ip_address'  => $request->ip(),
        ]);

        return back()->with('success', "Trauma record successfully linked to {$permanentPatient->name} ({$validated['verified_mrn']}).");
    }
}
