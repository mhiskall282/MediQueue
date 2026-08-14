<?php

namespace Tests\Unit;

use App\Models\QueueEntry;
use App\Models\Service;
use App\Models\User;
use App\Services\QueueService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class QueueServiceTest extends TestCase
{
    use RefreshDatabase;

    private QueueService $queueService;
    private User $patient1;
    private User $patient2;
    private User $patient3;
    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->queueService = new QueueService();
        $this->patient1     = User::factory()->create(['role' => 'patient']);
        $this->patient2     = User::factory()->create(['role' => 'patient']);
        $this->patient3     = User::factory()->create(['role' => 'patient']);
        $this->service      = Service::create([
            'name'                 => 'General Consultation',
            'prefix'               => 'GC',
            'avg_duration_minutes' => 10,
            'is_active'            => true,
        ]);
    }

    public function test_position_and_people_ahead_are_calculated_accurately(): void
    {
        $entry1 = $this->queueService->join($this->patient1, $this->service);
        $entry2 = $this->queueService->join($this->patient2, $this->service);
        $entry3 = $this->queueService->join($this->patient3, $this->service);

        $pos1 = $this->queueService->getPosition($entry1);
        $this->assertEquals(1, $pos1['position']);
        $this->assertEquals(0, $pos1['people_ahead']);

        $pos2 = $this->queueService->getPosition($entry2);
        $this->assertEquals(2, $pos2['position']);
        $this->assertEquals(1, $pos2['people_ahead']);

        $pos3 = $this->queueService->getPosition($entry3);
        $this->assertEquals(3, $pos3['position']);
        $this->assertEquals(2, $pos3['people_ahead']);
    }

    public function test_estimated_wait_time_formula_is_people_ahead_times_average_duration(): void
    {
        $this->queueService->join($this->patient1, $this->service);
        $this->queueService->join($this->patient2, $this->service);
        $entry3 = $this->queueService->join($this->patient3, $this->service);

        // 2 people ahead * 10 mins = 20 mins
        $waitMinutes = $this->queueService->getEstimatedWaitMinutes($entry3);
        $this->assertEquals(20, $waitMinutes);
    }

    public function test_invalid_state_transitions_throw_validation_exception(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $entry = $this->queueService->join($this->patient1, $this->service);

        // WAITING cannot directly transition to COMPLETED without CALLED & IN_SERVICE
        $this->expectException(ValidationException::class);
        $this->queueService->complete($entry, $staff);
    }

    public function test_state_machine_prevents_transitions_from_completed_terminal_state(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $entry = $this->queueService->join($this->patient1, $this->service);

        $this->queueService->callNext($this->service, $staff);
        $this->queueService->startService($entry->fresh(), $staff);
        $this->queueService->complete($entry->fresh(), $staff);

        $this->assertTrue($entry->fresh()->isTerminal());

        // Attempting to cancel a completed entry should fail
        $this->expectException(ValidationException::class);
        $this->queueService->cancel($entry->fresh(), $this->patient1);
    }
}
