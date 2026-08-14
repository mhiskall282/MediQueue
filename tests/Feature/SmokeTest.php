<?php

namespace Tests\Feature;

use App\Models\QueueEntry;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_guest_screens_render_successfully(): void
    {
        $this->get('/')->assertStatus(200)->assertSee('MediQueue');
        $this->get('/login')->assertStatus(200)->assertSee('Sign in to MediQueue');
        $this->get('/register')->assertStatus(200)->assertSee('Create your account');
        $this->get('/docs')->assertStatus(200)->assertSeeText('MediQueue Technical Reference');
    }

    public function test_all_patient_screens_render_successfully(): void
    {
        $patient = User::factory()->create(['role' => 'patient', 'name' => 'Jane Patient']);
        $service = Service::create([
            'name'                 => 'General Consultation',
            'prefix'               => 'GC',
            'avg_duration_minutes' => 15,
            'is_active'            => true,
        ]);

        $this->actingAs($patient)->get('/patient/dashboard')->assertStatus(200)->assertSeeText('Jane Patient');
        $this->actingAs($patient)->get('/patient/queue')->assertStatus(200)->assertSeeText('Select a Clinic Service');
        $this->actingAs($patient)->get("/patient/queue/service/{$service->id}")->assertStatus(200)->assertSeeText('Confirm');
        $this->actingAs($patient)->get('/patient/history')->assertStatus(200)->assertSeeText('Your Queue History');

        // Test ticket issue and status page
        $this->actingAs($patient)->post('/patient/queue', ['service_id' => $service->id]);
        $entry = QueueEntry::where('patient_id', $patient->id)->first();
        $this->actingAs($patient)->get("/patient/queue/{$entry->id}/status")->assertStatus(200)->assertSeeText('Live Queue Ticket');
    }

    public function test_all_staff_screens_render_successfully(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'name' => 'Dr. Sarah']);
        Service::create([
            'name'                 => 'General Consultation',
            'prefix'               => 'GC',
            'avg_duration_minutes' => 15,
            'is_active'            => true,
        ]);

        $this->actingAs($staff)->get('/staff/dashboard')->assertStatus(200)->assertSee('Queue Management');
    }

    public function test_all_admin_screens_render_successfully(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Super Admin']);
        $service = Service::create([
            'name'                 => 'General Consultation',
            'prefix'               => 'GC',
            'avg_duration_minutes' => 15,
            'is_active'            => true,
        ]);

        $this->actingAs($admin)->get('/admin/dashboard')->assertStatus(200)->assertSeeText('Admin Overview');
        $this->actingAs($admin)->get('/admin/services')->assertStatus(200)->assertSeeText('Clinic Services Catalogue');
        $this->actingAs($admin)->get('/admin/services/create')->assertStatus(200)->assertSeeText('Add Clinic Service');
        $this->actingAs($admin)->get("/admin/services/{$service->id}/edit")->assertStatus(200)->assertSeeText('Edit Service');
        $this->actingAs($admin)->get('/admin/users')->assertStatus(200)->assertSeeText('System Users');
        $this->actingAs($admin)->get('/admin/users/create')->assertStatus(200)->assertSeeText('Create Staff / Admin Account');
        $this->actingAs($admin)->get('/admin/audit')->assertStatus(200)->assertSeeText('System Audit Log');
    }
}
