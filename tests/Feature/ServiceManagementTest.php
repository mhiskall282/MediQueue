<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_create_service(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/services', [
            'name'                 => 'Dental Care',
            'prefix'               => 'DEN',
            'avg_duration_minutes' => 20,
            'description'          => 'Comprehensive dental care services',
        ]);

        $response->assertRedirect(route('admin.services.index'));

        $this->assertDatabaseHas('services', [
            'name'      => 'Dental Care',
            'prefix'    => 'DEN',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action'  => 'service.created',
        ]);
    }

    public function test_admin_can_edit_service(): void
    {
        $service = Service::create([
            'name'                 => 'Old Name',
            'prefix'               => 'OLD',
            'avg_duration_minutes' => 10,
            'is_active'            => true,
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/services/{$service->id}", [
            'name'                 => 'Updated Name',
            'prefix'               => 'NEW',
            'avg_duration_minutes' => 25,
            'description'          => 'Updated description',
        ]);

        $response->assertRedirect(route('admin.services.index'));

        $this->assertDatabaseHas('services', [
            'id'     => $service->id,
            'name'   => 'Updated Name',
            'prefix' => 'NEW',
        ]);
    }

    public function test_admin_can_toggle_service_active_state(): void
    {
        $service = Service::create([
            'name'                 => 'Radiology',
            'prefix'               => 'RAD',
            'avg_duration_minutes' => 15,
            'is_active'            => true,
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/services/{$service->id}/toggle");
        $response->assertRedirect(route('admin.services.index'));

        $this->assertFalse($service->fresh()->is_active);

        // Toggle back
        $this->actingAs($this->admin)->post("/admin/services/{$service->id}/toggle");
        $this->assertTrue($service->fresh()->is_active);
    }

    public function test_patient_cannot_join_inactive_service_queue(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);
        $service = Service::create([
            'name'                 => 'Closed Dept',
            'prefix'               => 'CLO',
            'avg_duration_minutes' => 10,
            'is_active'            => false,
        ]);

        $response = $this->actingAs($patient)->post('/patient/queue', [
            'service_id' => $service->id,
        ]);

        $response->assertSessionHasErrors('service_id');
    }
}
