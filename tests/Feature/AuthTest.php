<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_landing_page(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('MediQueue');
    }

    public function test_guest_can_view_login_page(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Welcome back');
    }

    public function test_guest_can_view_register_page(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Create your account');
    }

    public function test_patient_can_register(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Test Patient',
            'email'                 => 'newpatient@example.com',
            'phone'                 => '+60123456789',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('patient.dashboard'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'newpatient@example.com',
            'role'  => 'patient',
        ]);
    }

    public function test_patient_can_login_and_redirects_to_patient_dashboard(): void
    {
        $user = User::factory()->create([
            'role'      => 'patient',
            'password'  => bcrypt('password123'),
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('patient.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_staff_can_login_and_redirects_to_staff_dashboard(): void
    {
        $staff = User::factory()->create([
            'role'      => 'staff',
            'password'  => bcrypt('password123'),
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email'    => $staff->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('staff.dashboard'));
        $this->assertAuthenticatedAs($staff);
    }

    public function test_admin_can_login_and_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role'      => 'admin',
            'password'  => bcrypt('password123'),
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email'    => $admin->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_deactivated_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'role'      => 'patient',
            'password'  => bcrypt('password123'),
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create(['role' => 'patient']);

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }
}
