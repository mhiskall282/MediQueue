<?php

namespace Tests\Feature;

use App\Models\Bed;
use App\Models\DoctorRoster;
use App\Models\QueueEntry;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ClinicalReferralsAndOnCallTest extends TestCase
{
    use RefreshDatabase;

    private User $doctor;
    private User $labTech;
    private User $patient;
    private Service $clinicService;
    private Service $labService;
    private Bed $triageBay;

    protected function setUp(): void
    {
        parent::setUp();

        $this->doctor = User::factory()->create([
            'role'           => 'staff',
            'specialization' => 'Trauma & Emergency Medicine',
            'is_on_call'     => true,
            'on_call_shift'  => '24H_TRAUMA',
        ]);

        $this->labTech = User::factory()->create([
            'role'           => 'staff',
            'specialization' => 'Clinical Pathology & Laboratory',
            'is_on_call'     => true,
        ]);

        $this->patient = User::factory()->create([
            'role'        => 'patient',
            'hospital_id' => 'MRN-2026-99999',
        ]);

        $this->clinicService = Service::create([
            'name'                 => 'General Consultation',
            'prefix'               => 'GC',
            'avg_duration_minutes' => 15,
            'is_active'            => true,
        ]);

        $this->labService = Service::create([
            'name'                 => 'Diagnostic Laboratory',
            'prefix'               => 'LAB',
            'avg_duration_minutes' => 15,
            'is_active'            => true,
        ]);

        $this->triageBay = Bed::create([
            'ward_name'  => 'Emergency Wing',
            'bed_number' => 'TB-01',
            'bed_type'   => Bed::TYPE_TRIAGE_BAY,
            'status'     => Bed::STATUS_AVAILABLE,
        ]);
    }

    public function test_doctor_can_order_lab_tests_and_transfer_ticket(): void
    {
        $entry = QueueEntry::create([
            'patient_id'      => $this->patient->id,
            'service_id'      => $this->clinicService->id,
            'served_by'       => $this->doctor->id,
            'queue_number'    => 'GC-001',
            'sequence_number' => 1,
            'status'          => QueueEntry::STATUS_IN_SERVICE,
            'priority'        => 'NORMAL',
            'triage_level'    => 'GREEN',
        ]);

        $response = $this->actingAs($this->doctor)->post(route('staff.referral.order-lab', $entry), [
            'clinical_notes' => 'Suspected acute pancreatitis and electrolyte imbalance.',
            'lab_orders'     => 'Serum Amylase, Full Blood Count, Renal Profile',
        ]);

        $response->assertRedirect(route('staff.dashboard'));
        $response->assertSessionHas('success');

        $entry->refresh();
        $this->assertEquals($this->labService->id, $entry->service_id);
        $this->assertEquals(QueueEntry::STATUS_WAITING, $entry->status);
        $this->assertEquals(QueueEntry::STAGE_SENT_TO_LAB, $entry->clinical_workflow_stage);
        $this->assertEquals($this->doctor->id, $entry->referring_staff_id);
        $this->assertStringContainsString('Serum Amylase', $entry->lab_orders);
    }

    public function test_lab_technician_can_record_findings_and_loop_ticket_back_to_doctor(): void
    {
        $entry = QueueEntry::create([
            'patient_id'              => $this->patient->id,
            'service_id'              => $this->labService->id,
            'referring_staff_id'      => $this->doctor->id,
            'queue_number'            => 'GC-001',
            'sequence_number'         => 1,
            'status'                  => QueueEntry::STATUS_WAITING,
            'clinical_workflow_stage' => QueueEntry::STAGE_SENT_TO_LAB,
            'priority'                => 'NORMAL',
            'triage_level'            => 'GREEN',
        ]);

        $response = $this->actingAs($this->labTech)->post(route('staff.referral.complete-lab', $entry), [
            'lab_results' => 'WBC 14.5 (Elevated), Serum Amylase 450 U/L (High). Findings consistent with acute inflammation.',
        ]);

        $response->assertRedirect(route('staff.dashboard'));
        $response->assertSessionHas('success');

        $entry->refresh();
        $this->assertEquals(QueueEntry::STATUS_WAITING, $entry->status);
        $this->assertEquals(QueueEntry::STAGE_RETURNED_FOR_REVIEW, $entry->clinical_workflow_stage);
        $this->assertEquals('ORANGE', $entry->triage_level); // Priority retention for doctor review
        $this->assertEquals('URGENT', $entry->priority);
        $this->assertStringContainsString('Serum Amylase 450', $entry->lab_results);
    }

    public function test_doctor_can_discharge_patient_and_release_bed(): void
    {
        Mail::fake();

        $entry = QueueEntry::create([
            'patient_id'              => $this->patient->id,
            'service_id'              => $this->clinicService->id,
            'served_by'               => $this->doctor->id,
            'allocated_bed_id'        => $this->triageBay->id,
            'queue_number'            => 'GC-001',
            'sequence_number'         => 1,
            'status'                  => QueueEntry::STATUS_IN_SERVICE,
            'clinical_workflow_stage' => QueueEntry::STAGE_IN_CONSULTATION,
            'priority'                => 'NORMAL',
            'triage_level'            => 'GREEN',
        ]);

        $this->triageBay->update(['status' => Bed::STATUS_OCCUPIED, 'current_patient_id' => $this->patient->id]);

        $response = $this->actingAs($this->doctor)->post(route('staff.referral.discharge', $entry), [
            'discharge_summary' => 'Patient stabilized. Advised oral hydration and prescribed antibiotics.',
            'prescriptions'     => 'Amoxicillin 500mg TDS, Paracetamol 1g PRN',
        ]);

        $response->assertRedirect(route('staff.dashboard'));
        $response->assertSessionHas('success');

        $entry->refresh();
        $this->triageBay->refresh();

        $this->assertEquals(QueueEntry::STATUS_COMPLETED, $entry->status);
        $this->assertEquals(QueueEntry::STAGE_DISCHARGED, $entry->clinical_workflow_stage);
        $this->assertNull($entry->allocated_bed_id);
        $this->assertEquals(Bed::STATUS_AVAILABLE, $this->triageBay->status);
    }

    public function test_rapid_unconscious_trauma_intake_protocol(): void
    {
        $response = $this->actingAs($this->doctor)->post(route('staff.emergency.unconscious-intake'), [
            'estimated_gender' => 'MALE',
            'intake_notes'     => 'Unresponsive motor vehicle collision victim, GCS 6, airway compromised.',
            'vital_signs'      => 'BP 80/45, HR 140, SpO2 86%',
            'allocated_bay_id' => $this->triageBay->id,
        ]);

        $response->assertRedirect(route('staff.emergency.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('queue_entries', [
            'is_emergency_unconscious' => true,
            'triage_level'             => 'RED',
            'priority'                 => 'URGENT',
            'allocated_bed_id'         => $this->triageBay->id,
        ]);

        $this->triageBay->refresh();
        $this->assertEquals(Bed::STATUS_OCCUPIED, $this->triageBay->status);
    }

    public function test_staff_can_toggle_on_call_and_page_doctor(): void
    {
        // 1. Toggle On-Call
        $responseToggle = $this->actingAs($this->doctor)->post(route('staff.oncall.toggle', $this->doctor), [
            'is_on_call'     => false,
            'specialization' => 'Trauma & Emergency Medicine',
        ]);

        $responseToggle->assertRedirect();
        $this->doctor->refresh();
        $this->assertFalse($this->doctor->is_on_call);

        // 2. Set to On-Call
        $this->doctor->update(['is_on_call' => true]);

        // 3. Page Doctor
        $responsePage = $this->actingAs($this->labTech)->post(route('staff.oncall.page', $this->doctor), [
            'urgency_reason' => 'Emergency intubation needed in Trauma Bay 1',
            'location'       => 'Trauma Wing Bay 1',
        ]);

        $responsePage->assertRedirect();
        $responsePage->assertSessionHas('success');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->doctor->id,
            'type'    => 'urgent_doctor_page',
        ]);
    }
}
