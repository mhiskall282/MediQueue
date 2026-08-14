<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\QueueEntry;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * QueueService — Core queue business logic.
 *
 * All critical queue operations are performed within database transactions
 * to ensure atomicity and prevent race conditions (e.g., duplicate ticket numbers).
 * This service is the single source of truth for all queue state changes.
 */
class QueueService
{
    // ================================================================
    // Patient Operations
    // ================================================================

    /**
     * Enrol a patient in a service queue.
     *
     * This method:
     * 1. Validates the service is active
     * 2. Checks for existing active queue entry (prevents duplicates)
     * 3. Generates the next unique queue number atomically
     * 4. Creates the queue entry within a transaction
     * 5. Creates an in-app notification for the patient
     * 6. Records an audit log entry
     *
     * @throws ValidationException if the patient already has an active queue entry for this service
     * @throws \RuntimeException if the service is inactive
     */
    public function join(User $patient, Service $service): QueueEntry
    {
        if (!$service->is_active) {
            throw ValidationException::withMessages([
                'service_id' => 'This service is not currently accepting queue entries.',
            ]);
        }

        return DB::transaction(function () use ($patient, $service) {
            // Check for duplicate active entry
            $existing = QueueEntry::where('patient_id', $patient->id)
                ->where('service_id', $service->id)
                ->whereIn('status', QueueEntry::ACTIVE_STATUSES)
                ->whereDate('created_at', today())
                ->lockForUpdate()
                ->first();

            if ($existing) {
                throw ValidationException::withMessages([
                    'service_id' => "You already have an active queue entry ({$existing->queue_number}) for {$service->name}.",
                ]);
            }

            // Generate the next sequence number with a pessimistic lock
            $lastSequence = QueueEntry::where('service_id', $service->id)
                ->whereDate('created_at', today())
                ->lockForUpdate()
                ->max('sequence_number') ?? 0;

            $nextSequence = $lastSequence + 1;
            $queueNumber  = sprintf('%s-%03d', strtoupper($service->prefix), $nextSequence);

            // Create the queue entry
            $entry = QueueEntry::create([
                'patient_id'      => $patient->id,
                'service_id'      => $service->id,
                'queue_number'    => $queueNumber,
                'sequence_number' => $nextSequence,
                'status'          => QueueEntry::STATUS_WAITING,
                'priority'        => 'NORMAL',
                'joined_at'       => now(),
            ]);

            // Notify the patient
            $this->createNotification(
                $patient,
                'queue.joined',
                "Queue Ticket Issued — {$queueNumber}",
                "You have joined the queue for {$service->name}. Your number is {$queueNumber}.",
                ['queue_number' => $queueNumber, 'service_name' => $service->name, 'entry_id' => $entry->id]
            );

            // Audit log
            $this->recordAudit(
                $patient,
                'queue.joined',
                'QueueEntry',
                $entry->id,
                ['queue_number' => $queueNumber, 'service' => $service->name]
            );

            return $entry->load('service', 'patient');
        });
    }

    /**
     * Allow a patient to cancel their own WAITING queue entry.
     *
     * @throws ValidationException if transition is invalid
     */
    public function cancel(QueueEntry $entry, User $actor): QueueEntry
    {
        $this->assertCanTransitionTo($entry, QueueEntry::STATUS_CANCELLED);

        return DB::transaction(function () use ($entry, $actor) {
            $entry->update([
                'status'       => QueueEntry::STATUS_CANCELLED,
                'cancelled_at' => now(),
            ]);

            $this->createNotification(
                $entry->patient,
                'queue.cancelled',
                "Queue Entry Cancelled — {$entry->queue_number}",
                "Your queue entry {$entry->queue_number} for {$entry->service->name} has been cancelled.",
                ['queue_number' => $entry->queue_number]
            );

            $this->recordAudit(
                $actor,
                'queue.cancelled',
                'QueueEntry',
                $entry->id,
                ['queue_number' => $entry->queue_number, 'service' => $entry->service->name]
            );

            return $entry->refresh();
        });
    }

    // ================================================================
    // Staff Operations
    // ================================================================

    /**
     * Call the next WAITING patient in the queue for a service.
     *
     * Selects the next patient by:
     * 1. URGENT priority first
     * 2. Then by sequence number (FIFO)
     *
     * @throws ValidationException if no patients are waiting
     */
    public function callNext(Service $service, User $staff): QueueEntry
    {
        return DB::transaction(function () use ($service, $staff) {
            $next = QueueEntry::where('service_id', $service->id)
                ->whereDate('created_at', today())
                ->where('status', QueueEntry::STATUS_WAITING)
                ->byQueueOrder()
                ->lockForUpdate()
                ->first();

            if (!$next) {
                throw ValidationException::withMessages([
                    'queue' => 'No patients are currently waiting for this service.',
                ]);
            }

            $next->update([
                'status'    => QueueEntry::STATUS_CALLED,
                'called_at' => now(),
                'served_by' => $staff->id,
            ]);

            $this->createNotification(
                $next->patient,
                'queue.called',
                "You Have Been Called — {$next->queue_number}",
                "Please proceed to the consultation area. Your number {$next->queue_number} has been called for {$service->name}.",
                ['queue_number' => $next->queue_number, 'service_name' => $service->name]
            );

            $this->recordAudit(
                $staff,
                'queue.called',
                'QueueEntry',
                $next->id,
                ['queue_number' => $next->queue_number, 'patient' => $next->patient->name, 'service' => $service->name]
            );

            return $next->load('patient', 'service');
        });
    }

    /**
     * Start service for a CALLED patient.
     *
     * @throws ValidationException if transition is invalid
     */
    public function startService(QueueEntry $entry, User $staff): QueueEntry
    {
        $this->assertCanTransitionTo($entry, QueueEntry::STATUS_IN_SERVICE);

        return DB::transaction(function () use ($entry, $staff) {
            $entry->update([
                'status'             => QueueEntry::STATUS_IN_SERVICE,
                'service_started_at' => now(),
                'served_by'          => $staff->id,
            ]);

            $this->createNotification(
                $entry->patient,
                'queue.serving',
                "Service Started — {$entry->queue_number}",
                "Service has started for your queue number {$entry->queue_number} at {$entry->service->name}.",
                ['queue_number' => $entry->queue_number]
            );

            $this->recordAudit(
                $staff,
                'queue.service_started',
                'QueueEntry',
                $entry->id,
                ['queue_number' => $entry->queue_number, 'patient' => $entry->patient->name]
            );

            return $entry->refresh();
        });
    }

    /**
     * Complete service for an IN_SERVICE patient.
     *
     * @throws ValidationException if transition is invalid
     */
    public function complete(QueueEntry $entry, User $staff): QueueEntry
    {
        $this->assertCanTransitionTo($entry, QueueEntry::STATUS_COMPLETED);

        return DB::transaction(function () use ($entry, $staff) {
            $entry->update([
                'status'       => QueueEntry::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);

            $this->createNotification(
                $entry->patient,
                'queue.completed',
                "Service Completed — {$entry->queue_number}",
                "Your service for {$entry->service->name} has been completed. Thank you for visiting.",
                ['queue_number' => $entry->queue_number]
            );

            $this->recordAudit(
                $staff,
                'queue.completed',
                'QueueEntry',
                $entry->id,
                ['queue_number' => $entry->queue_number, 'patient' => $entry->patient->name]
            );

            return $entry->refresh();
        });
    }

    /**
     * Skip a CALLED patient (no response).
     *
     * @throws ValidationException if transition is invalid
     */
    public function skip(QueueEntry $entry, User $staff): QueueEntry
    {
        $this->assertCanTransitionTo($entry, QueueEntry::STATUS_SKIPPED);

        return DB::transaction(function () use ($entry, $staff) {
            $entry->update([
                'status'     => QueueEntry::STATUS_SKIPPED,
                'skipped_at' => now(),
            ]);

            $this->createNotification(
                $entry->patient,
                'queue.skipped',
                "Queue Entry Skipped — {$entry->queue_number}",
                "Your number {$entry->queue_number} was skipped. Please speak to the reception desk to be re-queued.",
                ['queue_number' => $entry->queue_number]
            );

            $this->recordAudit(
                $staff,
                'queue.skipped',
                'QueueEntry',
                $entry->id,
                ['queue_number' => $entry->queue_number, 'patient' => $entry->patient->name]
            );

            return $entry->refresh();
        });
    }

    /**
     * Recall a SKIPPED patient back to CALLED status.
     *
     * @throws ValidationException if transition is invalid
     */
    public function recall(QueueEntry $entry, User $staff): QueueEntry
    {
        $this->assertCanTransitionTo($entry, QueueEntry::STATUS_CALLED);

        return DB::transaction(function () use ($entry, $staff) {
            $entry->update([
                'status'     => QueueEntry::STATUS_CALLED,
                'called_at'  => now(),
                'skipped_at' => null,
                'served_by'  => $staff->id,
            ]);

            $this->createNotification(
                $entry->patient,
                'queue.recalled',
                "Please Return — {$entry->queue_number}",
                "Your number {$entry->queue_number} has been recalled. Please report to the consultation area now.",
                ['queue_number' => $entry->queue_number]
            );

            $this->recordAudit(
                $staff,
                'queue.recalled',
                'QueueEntry',
                $entry->id,
                ['queue_number' => $entry->queue_number, 'patient' => $entry->patient->name]
            );

            return $entry->refresh();
        });
    }

    // ================================================================
    // Position & Wait Time Calculation
    // ================================================================

    /**
     * Calculate the patient's current position in the queue.
     * Position = number of WAITING entries ahead of this entry.
     *
     * @return array{position: int, people_ahead: int, currently_serving: string|null}
     */
    public function getPosition(QueueEntry $entry): array
    {
        if ($entry->status !== QueueEntry::STATUS_WAITING) {
            return [
                'position'          => 0,
                'people_ahead'      => 0,
                'currently_serving' => null,
            ];
        }

        $peopleAhead = QueueEntry::where('service_id', $entry->service_id)
            ->whereDate('created_at', today())
            ->where('status', QueueEntry::STATUS_WAITING)
            ->where(function ($q) use ($entry) {
                $q->whereRaw("CASE priority WHEN 'URGENT' THEN 0 ELSE 1 END < CASE ? WHEN 'URGENT' THEN 0 ELSE 1 END", [$entry->priority])
                  ->orWhere(function ($inner) use ($entry) {
                      $inner->where('priority', $entry->priority)
                            ->where('sequence_number', '<', $entry->sequence_number);
                  });
            })
            ->count();

        $position = $peopleAhead + 1;

        $currentlyServing = QueueEntry::where('service_id', $entry->service_id)
            ->whereDate('created_at', today())
            ->whereIn('status', [QueueEntry::STATUS_CALLED, QueueEntry::STATUS_IN_SERVICE])
            ->latest('called_at')
            ->value('queue_number');

        return [
            'position'          => $position,
            'people_ahead'      => $peopleAhead,
            'currently_serving' => $currentlyServing,
        ];
    }

    /**
     * Calculate estimated wait time in minutes.
     * Formula: people_ahead × avg_duration_minutes
     * Clearly labelled as an estimate.
     */
    public function getEstimatedWaitMinutes(QueueEntry $entry): int
    {
        $positionData = $this->getPosition($entry);
        return $positionData['people_ahead'] * ($entry->service->avg_duration_minutes ?? 15);
    }

    // ================================================================
    // Private Helpers
    // ================================================================

    /**
     * Assert that a queue entry can transition to the given status.
     *
     * @throws ValidationException
     */
    private function assertCanTransitionTo(QueueEntry $entry, string $newStatus): void
    {
        if (!$entry->canTransitionTo($newStatus)) {
            throw ValidationException::withMessages([
                'status' => "Cannot transition queue entry from {$entry->status} to {$newStatus}. Invalid state transition.",
            ]);
        }
    }

    /**
     * Create an in-app notification for a user.
     */
    private function createNotification(
        User $user,
        string $type,
        string $title,
        string $body,
        array $data = []
    ): Notification {
        return Notification::create([
            'user_id' => $user->id,
            'type'    => $type,
            'title'   => $title,
            'body'    => $body,
            'data'    => $data,
        ]);
    }

    /**
     * Record an audit log entry.
     */
    private function recordAudit(
        User $actor,
        string $action,
        string $entityType,
        ?int $entityId = null,
        array $metadata = []
    ): AuditLog {
        return AuditLog::create([
            'user_id'     => $actor->id,
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'metadata'    => $metadata,
            'ip_address'  => request()->ip(),
        ]);
    }
}
