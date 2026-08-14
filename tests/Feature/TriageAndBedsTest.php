<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Bed;
use App\Models\QueueEntry;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TriageAndBedsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $staff;
    private User $patient;
    private Service $service;
    private Bed $bed;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin   = User::factory()->create(['role' => 'admin']);
        $this->staff   = User::factory()->create(['role' => 'staff']);
        $this->patient = User::factory()->create(['role' => 'patient']);

        $this->service = Service::create([
            'name'                 => 'Emergency & Trauma Care',
            'prefix'               => 'EMG',
            'avg_duration_minutes' => 20,
            'is_active'            => true,
        ]);

        $this->bed = Bed::create([
            'ward_name'   => 'Emergency Wing',
            'bed_number'  => 'TB-01',
            'bed_type'    => Bed::TYPE_TRIAGE_BAY,
            'status'      => Bed::STATUS_AVAILABLE,
        ]);
    }

    public function test_staff_can_update_triage_level(): void
    {
        $entry = QueueEntry::create([
            'patient_id'      => $this->patient->id,
            'service_id'      => $this->service->id,
            'queue_number'    => 'EMG-001',
            'sequence_number' => 1,
            'status'          => QueueEntry::STATUS_WAITING,
            'priority'        => 'NORMAL',
            'triage_level'    => 'GREEN',
        ]);

        $response = $this->actingAs($this->staff)->post(route('staff.queue.triage', $entry), [
            'triage_level' => 'RED',
            'triage_notes' => 'Patient exhibiting severe chest pain and dyspnea.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $entry->refresh();
        $this->assertEquals('RED', $entry->triage_level);
        $this->assertEquals('URGENT', $entry->priority);
        $this->assertStringContainsString('severe chest pain', $entry->triage_notes);
    }

    public function test_staff_can_allocate_and_release_hospital_bed(): void
    {
        $entry = QueueEntry::create([
            'patient_id'      => $this->patient->id,
            'service_id'      => $this->service->id,
            'queue_number'    => 'EMG-001',
            'sequence_number' => 1,
            'status'          => QueueEntry::STATUS_CALLED,
            'priority'        => 'NORMAL',
            'triage_level'    => 'ORANGE',
        ]);

        // 1. Allocate bed
        $responseAllocate = $this->actingAs($this->staff)->post(route('staff.queue.allocate-bed', $entry), [
            'bed_id' => $this->bed->id,
        ]);

        $responseAllocate->assertRedirect();
        $responseAllocate->assertSessionHas('success');

        $this->bed->refresh();
        $entry->refresh();

        $this->assertEquals(Bed::STATUS_OCCUPIED, $this->bed->status);
        $this->assertEquals($this->patient->id, $this->bed->current_patient_id);
        $this->assertEquals($this->bed->id, $entry->allocated_bed_id);

        // 2. Release bed
        $responseRelease = $this->actingAs($this->staff)->post(route('staff.queue.release-bed', $entry));
        $responseRelease->assertRedirect();

        $this->bed->refresh();
        $entry->refresh();

        $this->assertEquals(Bed::STATUS_AVAILABLE, $this->bed->status);
        $this->assertNull($entry->allocated_bed_id);
    }

    public function test_patient_can_book_advance_appointment(): void
    {
        $response = $this->actingAs($this->patient)->post(route('patient.appointments.store'), [
            'service_id'       => $this->service->id,
            'appointment_date' => now()->addDays(2)->toDateString(),
            'time_slot'        => '10:30 AM',
            'symptoms_notes'   => 'Routine checkup and prescription refill',
        ]);

        $response->assertRedirect(route('patient.appointments.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('appointments', [
            'patient_id' => $this->patient->id,
            'service_id' => $this->service->id,
            'time_slot'  => '10:30 AM',
            'status'     => Appointment::STATUS_BOOKED,
        ]);
    }

    public function test_staff_can_check_in_appointment_and_generate_queue_ticket(): void
    {
        $appointment = Appointment::create([
            'patient_id'       => $this->patient->id,
            'service_id'       => $this->service->id,
            'appointment_date' => now()->toDateString(),
            'time_slot'        => '09:00 AM',
            'status'           => Appointment::STATUS_BOOKED,
        ]);

        $response = $this->actingAs($this->staff)->post(route('staff.appointments.check-in', $appointment));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $appointment->refresh();
        $this->assertEquals(Appointment::STATUS_CHECKED_IN, $appointment->status);
        $this->assertNotNull($appointment->generated_queue_entry_id);

        $this->assertDatabaseHas('queue_entries', [
            'id'         => $appointment->generated_queue_entry_id,
            'patient_id' => $this->patient->id,
            'service_id' => $this->service->id,
            'status'     => QueueEntry::STATUS_WAITING,
        ]);
    }
}
