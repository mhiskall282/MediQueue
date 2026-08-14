<?php

namespace Tests\Feature;

use App\Mail\QueueNotificationMail;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use App\Services\QueueService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_hospital_display_screen_and_data_endpoint_are_publicly_accessible(): void
    {
        $service = Service::create([
            'name'                 => 'Emergency Care',
            'prefix'               => 'EMG',
            'avg_duration_minutes' => 10,
            'is_active'            => true,
        ]);

        $response = $this->get('/display');
        $response->assertStatus(200);
        $response->assertSee('Outpatient Queue Display');

        $dataResp = $this->getJson('/display/data');
        $dataResp->assertStatus(200);
        $dataResp->assertJsonStructure([
            'called',
            'departments',
            'time',
            'date',
        ]);
    }

    public function test_admin_can_view_and_update_settings(): void
    {
        Setting::create([
            'key'         => 'clinic_name',
            'value'       => 'Old Clinic Name',
            'group'       => 'general',
            'label'       => 'Clinic Name',
            'type'        => 'text',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/settings');
        $response->assertStatus(200);
        $response->assertSee('System & Clinic Settings');

        $updateResp = $this->actingAs($this->admin)->put('/admin/settings', [
            'clinic_name' => 'Brand New Health Center',
        ]);

        $updateResp->assertRedirect(route('admin.settings.index'));
        $this->assertEquals('Brand New Health Center', Setting::get('clinic_name'));

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action'  => 'settings.updated',
        ]);
    }

    public function test_admin_can_edit_user_and_change_role(): void
    {
        $targetUser = User::factory()->create(['role' => 'patient', 'name' => 'Old Name']);

        $response = $this->actingAs($this->admin)->get("/admin/users/{$targetUser->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Edit User: Old Name');

        $updateResp = $this->actingAs($this->admin)->put("/admin/users/{$targetUser->id}", [
            'name'  => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '+60111222333',
            'role'  => 'staff',
        ]);

        $updateResp->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id'    => $targetUser->id,
            'name'  => 'Updated Name',
            'email' => 'updated@example.com',
            'role'  => 'staff',
        ]);
    }

    public function test_admin_can_reset_user_password(): void
    {
        Mail::fake();

        $targetUser = User::factory()->create([
            'password' => Hash::make('oldpassword123'),
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/users/{$targetUser->id}/reset-password", [
            'password'              => 'NewSecretPassword99!',
            'password_confirmation' => 'NewSecretPassword99!',
        ]);

        $response->assertRedirect(route('admin.users.index'));

        // Check password changed
        $this->assertTrue(Hash::check('NewSecretPassword99!', $targetUser->fresh()->password));

        // Check audit log and notification
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action'  => 'user.password_reset',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $targetUser->id,
            'type'    => 'auth.password_reset',
        ]);

        // Check email dispatched
        Mail::assertSent(QueueNotificationMail::class, function ($mail) use ($targetUser) {
            return $mail->hasTo($targetUser->email) && $mail->subjectLine === 'Password Reset Notice';
        });
    }

    public function test_queue_lifecycle_triggers_email_dispatch(): void
    {
        Mail::fake();

        $patient = User::factory()->create(['role' => 'patient', 'email' => 'patient@test.com']);
        $service = Service::create([
            'name'                 => 'General Consultation',
            'prefix'               => 'GC',
            'avg_duration_minutes' => 15,
            'is_active'            => true,
        ]);

        $queueService = new QueueService();
        $entry = $queueService->join($patient, $service);

        // Verify email was sent for queue.joined
        Mail::assertSent(QueueNotificationMail::class, function ($mail) use ($patient) {
            return $mail->hasTo($patient->email) && str_contains($mail->subjectLine, 'Queue Ticket Issued');
        });
    }
}
