<?php

namespace Tests\Feature;

use App\Models\QueueEntry;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QueueLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private User $patient;
    private User $staff;
    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->patient = User::factory()->create(['role' => 'patient', 'name' => 'John Patient']);
        $this->staff   = User::factory()->create(['role' => 'staff',   'name' => 'Dr. Smith']);
        $this->service = Service::create([
            'name'                 => 'General Consultation',
            'prefix'               => 'GC',
            'avg_duration_minutes' => 15,
            'is_active'            => true,
        ]);
    }

    public function test_patient_can_join_active_queue(): void
    {
        $response = $this->actingAs($this->patient)->post('/patient/queue', [
            'service_id' => $this->service->id,
        ]);

        $entry = QueueEntry::first();
        $this->assertNotNull($entry);
        $this->assertEquals('GC-001', $entry->queue_number);
        $this->assertEquals(1, $entry->sequence_number);
        $this->assertEquals('WAITING', $entry->status);
        $this->assertEquals($this->patient->id, $entry->patient_id);

        $response->assertRedirect(route('patient.queue.status', $entry));

        // Check in-app notification and audit log created
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->patient->id,
            'type'    => 'queue.joined',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->patient->id,
            'action'  => 'queue.joined',
        ]);
    }

    public function test_sequential_ticket_numbers_increment_properly(): void
    {
        $patient2 = User::factory()->create(['role' => 'patient']);

        $this->actingAs($this->patient)->post('/patient/queue', ['service_id' => $this->service->id]);
        $this->actingAs($patient2)->post('/patient/queue', ['service_id' => $this->service->id]);

        $entries = QueueEntry::orderBy('sequence_number')->get();
        $this->assertCount(2, $entries);
        $this->assertEquals('GC-001', $entries[0]->queue_number);
        $this->assertEquals('GC-002', $entries[1]->queue_number);
    }

    public function test_patient_cannot_have_duplicate_active_queue_tickets_for_same_service(): void
    {
        $this->actingAs($this->patient)->post('/patient/queue', ['service_id' => $this->service->id]);

        // Attempt second join for same service
        $response = $this->actingAs($this->patient)->post('/patient/queue', ['service_id' => $this->service->id]);
        $response->assertSessionHasErrors('service_id');

        $this->assertEquals(1, QueueEntry::where('patient_id', $this->patient->id)->count());
    }

    public function test_patient_can_cancel_waiting_ticket(): void
    {
        $this->actingAs($this->patient)->post('/patient/queue', ['service_id' => $this->service->id]);
        $entry = QueueEntry::first();

        $response = $this->actingAs($this->patient)->post("/patient/queue/{$entry->id}/cancel");

        $response->assertRedirect(route('patient.dashboard'));
        $this->assertEquals('CANCELLED', $entry->fresh()->status);
        $this->assertNotNull($entry->fresh()->cancelled_at);
    }

    public function test_staff_can_call_next_patient(): void
    {
        $this->actingAs($this->patient)->post('/patient/queue', ['service_id' => $this->service->id]);
        $entry = QueueEntry::first();

        $response = $this->actingAs($this->staff)->post('/staff/queue/call-next', [
            'service_id' => $this->service->id,
        ]);

        $response->assertRedirect(route('staff.dashboard', ['service_id' => $this->service->id]));

        $updated = $entry->fresh();
        $this->assertEquals('CALLED', $updated->status);
        $this->assertNotNull($updated->called_at);
        $this->assertEquals($this->staff->id, $updated->served_by);

        // Patient notification sent
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->patient->id,
            'type'    => 'queue.called',
        ]);
    }

    public function test_staff_can_start_service(): void
    {
        $this->actingAs($this->patient)->post('/patient/queue', ['service_id' => $this->service->id]);
        $this->actingAs($this->staff)->post('/staff/queue/call-next', ['service_id' => $this->service->id]);
        $entry = QueueEntry::first();

        $response = $this->actingAs($this->staff)->post("/staff/queue/{$entry->id}/start");
        $response->assertRedirect();

        $updated = $entry->fresh();
        $this->assertEquals('IN_SERVICE', $updated->status);
        $this->assertNotNull($updated->service_started_at);
    }

    public function test_staff_can_complete_service(): void
    {
        $this->actingAs($this->patient)->post('/patient/queue', ['service_id' => $this->service->id]);
        $this->actingAs($this->staff)->post('/staff/queue/call-next', ['service_id' => $this->service->id]);
        $entry = QueueEntry::first();
        $this->actingAs($this->staff)->post("/staff/queue/{$entry->id}/start");

        $response = $this->actingAs($this->staff)->post("/staff/queue/{$entry->id}/complete");
        $response->assertRedirect();

        $updated = $entry->fresh();
        $this->assertEquals('COMPLETED', $updated->status);
        $this->assertNotNull($updated->completed_at);
    }

    public function test_staff_can_skip_and_recall_patient(): void
    {
        $this->actingAs($this->patient)->post('/patient/queue', ['service_id' => $this->service->id]);
        $this->actingAs($this->staff)->post('/staff/queue/call-next', ['service_id' => $this->service->id]);
        $entry = QueueEntry::first();

        // Skip
        $this->actingAs($this->staff)->post("/staff/queue/{$entry->id}/skip");
        $this->assertEquals('SKIPPED', $entry->fresh()->status);
        $this->assertNotNull($entry->fresh()->skipped_at);

        // Recall
        $this->actingAs($this->staff)->post("/staff/queue/{$entry->id}/recall");
        $this->assertEquals('CALLED', $entry->fresh()->status);
    }

    public function test_patient_can_poll_json_status(): void
    {
        $this->actingAs($this->patient)->post('/patient/queue', ['service_id' => $this->service->id]);
        $entry = QueueEntry::first();

        $response = $this->actingAs($this->patient)->getJson("/patient/queue/{$entry->id}/status.json");

        $response->assertStatus(200);
        $response->assertJson([
            'status'       => 'WAITING',
            'queue_number' => 'GC-001',
            'position'     => 1,
            'people_ahead' => 0,
        ]);
    }
}
