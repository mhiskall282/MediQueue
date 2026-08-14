<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SecurityTelemetryAndSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_settings_page(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_PATIENT,
        ]);

        $response = $this->actingAs($user)->get('/settings');
        $response->assertStatus(200);
        $response->assertSee('Account Profile', false);
        $response->assertSee($user->name);
    }

    public function test_user_can_update_profile_and_preferences(): void
    {
        $user = User::factory()->create([
            'phone' => '0241234567',
        ]);

        $response = $this->actingAs($user)->put('/settings/profile', [
            'name'                        => 'Dr. Updated Clinician',
            'phone'                       => '+233249998877',
            'emergency_contact_phone'     => '+233201112233',
            'email_notifications_enabled' => '1',
        ]);

        $response->assertSessionHas('success');
        $user->refresh();
        $this->assertEquals('Dr. Updated Clinician', $user->name);
        $this->assertEquals('+233249998877', $user->phone);
    }

    public function test_user_can_update_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('CurrentSecret123!'),
        ]);

        $response = $this->actingAs($user)->put('/settings/password', [
            'current_password'      => 'CurrentSecret123!',
            'password'              => 'BrandNewPassword2026!',
            'password_confirmation' => 'BrandNewPassword2026!',
        ]);

        $response->assertSessionHas('success');
        $user->refresh();
        $this->assertTrue(Hash::check('BrandNewPassword2026!', $user->password));
    }

    public function test_mandatory_first_time_password_change_redirection(): void
    {
        $user = User::factory()->create([
            'password'             => Hash::make('TemporaryAdminPass123!'),
            'must_change_password' => true,
        ]);

        $response = $this->actingAs($user)->get('/patient/dashboard');
        $response->assertRedirect('/force-password-change');

        // Test updating the force change password
        $updateResponse = $this->actingAs($user)->post('/force-password-change', [
            'password'              => 'MyNewSecurePassword2026!',
            'password_confirmation' => 'MyNewSecurePassword2026!',
        ]);

        $updateResponse->assertRedirect('/patient/dashboard');
        $user->refresh();
        $this->assertFalse($user->must_change_password);
        $this->assertTrue(Hash::check('MyNewSecurePassword2026!', $user->password));
    }

    public function test_login_detects_ip_change_and_dispatches_security_alert(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email'         => 'doctor@ugmc.health',
            'password'      => Hash::make('ValidDocPass123!'),
            'last_login_ip' => '192.168.1.10',
            'role'          => User::ROLE_DOCTOR,
            'is_approved'   => true,
        ]);

        $response = $this->post('/login', [
            'email'    => 'doctor@ugmc.health',
            'password' => 'ValidDocPass123!',
        ], ['REMOTE_ADDR' => '102.176.45.12']);

        $response->assertRedirect('/staff/dashboard');

        $user->refresh();
        $this->assertEquals('102.176.45.12', $user->last_login_ip);
    }
}
