<?php

namespace Tests\Feature;

use App\Models\ClinicalMessage;
use App\Models\QueueEntry;
use App\Models\SecurityAlert;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GranularRolesAndComplianceTest extends TestCase
{
    use RefreshDatabase;

    public function test_medical_staff_registration_creates_pending_unapproved_account(): void
    {
        $response = $this->post(route('register'), [
            'name'                   => 'Dr. Gregory House',
            'email'                  => 'greg.house@hospital.org',
            'phone'                  => '+60123456789',
            'role'                   => User::ROLE_DOCTOR,
            'medical_license_number' => 'MMC-998822',
            'specialization'         => 'Diagnostic Medicine',
            'password'               => 'password123',
            'password_confirmation'  => 'password123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        $user = User::where('email', 'greg.house@hospital.org')->first();
        $this->assertNotNull($user);
        $this->assertEquals(User::ROLE_DOCTOR, $user->role);
        $this->assertFalse($user->is_approved);
        $this->assertEquals('MMC-998822', $user->medical_license_number);
    }

    public function test_unapproved_medical_staff_cannot_login(): void
    {
        $doctor = User::factory()->create([
            'role'                   => User::ROLE_DOCTOR,
            'is_approved'            => false,
            'medical_license_number' => 'MMC-112233',
            'password'               => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email'    => $doctor->email,
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_admin_can_verify_and_approve_pending_medical_staff(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'is_approved' => true]);
        $doctor = User::factory()->create([
            'role'        => User::ROLE_DOCTOR,
            'is_approved' => false,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.users.approve', $doctor));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $doctor->refresh();
        $this->assertTrue($doctor->is_approved);
        $this->assertEquals($admin->id, $doctor->approved_by);
    }

    public function test_admin_can_revoke_staff_privileges(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'is_approved' => true]);
        $doctor = User::factory()->create(['role' => User::ROLE_DOCTOR, 'is_approved' => true]);

        $response = $this->actingAs($admin)->post(route('admin.users.revoke', $doctor));

        $response->assertRedirect();
        $doctor->refresh();
        $this->assertFalse($doctor->is_approved);
        $this->assertFalse($doctor->is_active);
    }

    public function test_clinical_staff_can_dispatch_inter_staff_message(): void
    {
        $doctor = User::factory()->create(['role' => User::ROLE_DOCTOR, 'is_approved' => true]);
        $pharmacist = User::factory()->create(['role' => User::ROLE_PHARMACIST, 'is_approved' => true]);

        $response = $this->actingAs($doctor)->post(route('staff.messages.store'), [
            'recipient_id' => $pharmacist->id,
            'subject'      => 'STAT Antibiotic Dosage Confirmation',
            'message'      => 'Please confirm IV vancomycin trough levels for trauma bed 2.',
            'urgency'      => ClinicalMessage::URGENCY_STAT_EMERGENCY,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('clinical_messages', [
            'sender_id'    => $doctor->id,
            'recipient_id' => $pharmacist->id,
            'urgency'      => ClinicalMessage::URGENCY_STAT_EMERGENCY,
            'subject'      => 'STAT Antibiotic Dosage Confirmation',
        ]);
    }

    public function test_admin_can_monitor_and_resolve_hipaa_security_alerts(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'is_approved' => true]);
        $alert = SecurityAlert::create([
            'event_type'  => 'BRUTE_FORCE_LOGIN_ATTEMPT',
            'severity'    => SecurityAlert::SEVERITY_HIGH,
            'description' => 'Multiple failed logins detected from IP 192.168.1.50',
            'ip_address'  => '192.168.1.50',
        ]);

        $viewResponse = $this->actingAs($admin)->get(route('admin.security.index'));
        $viewResponse->assertOk();
        $viewResponse->assertSee('BRUTE_FORCE_LOGIN_ATTEMPT');

        $resolveResponse = $this->actingAs($admin)->post(route('admin.security.resolve', $alert));
        $resolveResponse->assertRedirect();

        $alert->refresh();
        $this->assertTrue($alert->is_resolved);
        $this->assertEquals($admin->id, $alert->resolved_by);
    }
}
