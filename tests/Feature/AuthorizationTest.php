<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_patient_dashboard(): void
    {
        $response = $this->get('/patient/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_access_staff_dashboard(): void
    {
        $response = $this->get('/staff/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_patient_cannot_access_staff_dashboard(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);

        $response = $this->actingAs($patient)->get('/staff/dashboard');
        $response->assertStatus(403);
    }

    public function test_patient_cannot_access_admin_dashboard(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);

        $response = $this->actingAs($patient)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_staff_cannot_access_admin_dashboard(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_staff_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/staff/dashboard');
        $response->assertStatus(200);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
    }
}
