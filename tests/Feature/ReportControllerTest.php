<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\QueueEntry;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $staff;
    private User $patient;
    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin   = User::factory()->create(['role' => 'admin']);
        $this->staff   = User::factory()->create(['role' => 'staff']);
        $this->patient = User::factory()->create(['role' => 'patient']);

        $this->service = Service::create([
            'name'                 => 'General Consultation',
            'prefix'               => 'GC',
            'avg_duration_minutes' => 15,
            'is_active'            => true,
        ]);
    }

    public function test_admin_can_view_reports_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.index'));

        $response->assertStatus(200);
        $response->assertSee('Clinic Reports');
        $response->assertSee('Export to CSV');
        $response->assertSee('Email Summary to Admin');
    }

    public function test_non_admin_cannot_access_reports(): void
    {
        $responseStaff = $this->actingAs($this->staff)->get(route('admin.reports.index'));
        $responseStaff->assertStatus(403);

        $responsePatient = $this->actingAs($this->patient)->get(route('admin.reports.index'));
        $responsePatient->assertStatus(403);
    }

    public function test_admin_can_export_csv_report(): void
    {
        QueueEntry::create([
            'patient_id'      => $this->patient->id,
            'service_id'      => $this->service->id,
            'served_by'       => $this->staff->id,
            'queue_number'    => 'GC-001',
            'sequence_number' => 1,
            'status'          => QueueEntry::STATUS_COMPLETED,
            'priority'        => 'NORMAL',
            'joined_at'       => now()->subMinutes(30),
            'called_at'       => now()->subMinutes(20),
            'service_started_at' => now()->subMinutes(15),
            'completed_at'    => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('GC-001', $response->streamedContent());
        $this->assertStringContainsString('General Consultation', $response->streamedContent());
    }

    public function test_admin_can_dispatch_email_report(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->admin)->post(route('admin.reports.email'), [
            'start_date' => now()->toDateString(),
            'end_date'   => now()->toDateString(),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Mail::assertSent(\App\Mail\QueueNotificationMail::class);
    }

    public function test_admin_can_view_forensic_investigation(): void
    {
        $entry = QueueEntry::create([
            'patient_id'      => $this->patient->id,
            'service_id'      => $this->service->id,
            'served_by'       => $this->staff->id,
            'queue_number'    => 'GC-001',
            'sequence_number' => 1,
            'status'          => QueueEntry::STATUS_COMPLETED,
            'priority'        => 'NORMAL',
            'joined_at'       => now()->subMinutes(30),
            'called_at'       => now()->subMinutes(20),
            'service_started_at' => now()->subMinutes(15),
            'completed_at'    => now(),
        ]);

        AuditLog::create([
            'user_id'     => $this->staff->id,
            'action'      => 'queue.called',
            'entity_type' => 'QueueEntry',
            'entity_id'   => $entry->id,
            'metadata'    => ['queue_number' => 'GC-001'],
            'ip_address'  => '192.168.1.100',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.investigate', $entry));

        $response->assertStatus(200);
        $response->assertSee('Chain of Custody');
        $response->assertSee('GC-001');
        $response->assertSee('192.168.1.100');
        $response->assertSee('queue.called');
    }
}
