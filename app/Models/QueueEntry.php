<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QueueEntry extends Model
{
    use HasFactory;

    /**
     * Valid queue status values.
     */
    public const STATUS_WAITING    = 'WAITING';
    public const STATUS_CALLED     = 'CALLED';
    public const STATUS_IN_SERVICE = 'IN_SERVICE';
    public const STATUS_COMPLETED  = 'COMPLETED';
    public const STATUS_CANCELLED  = 'CANCELLED';
    public const STATUS_SKIPPED    = 'SKIPPED';

    /**
     * Active statuses — entries still in the queue flow.
     */
    public const ACTIVE_STATUSES = ['WAITING', 'CALLED', 'IN_SERVICE'];

    /**
     * Terminal statuses — entries that have reached their final state.
     */
    public const TERMINAL_STATUSES = ['COMPLETED', 'CANCELLED'];

    /**
     * Valid state transitions: [from_status => [allowed_to_statuses]].
     * This enforces the queue state machine in the backend.
     */
    public const VALID_TRANSITIONS = [
        'WAITING'    => ['CALLED', 'CANCELLED'],
        'CALLED'     => ['IN_SERVICE', 'SKIPPED', 'CANCELLED'],
        'IN_SERVICE' => ['COMPLETED'],
        'SKIPPED'    => ['CALLED', 'CANCELLED'],
        'COMPLETED'  => [],   // Terminal — no transitions out
        'CANCELLED'  => [],   // Terminal — no transitions out
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'patient_id',
        'service_id',
        'served_by',
        'queue_number',
        'sequence_number',
        'status',
        'priority',
        'joined_at',
        'called_at',
        'service_started_at',
        'completed_at',
        'cancelled_at',
        'skipped_at',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'joined_at'          => 'datetime',
            'called_at'          => 'datetime',
            'service_started_at' => 'datetime',
            'completed_at'       => 'datetime',
            'cancelled_at'       => 'datetime',
            'skipped_at'         => 'datetime',
        ];
    }

    // ================================================================
    // Relationships
    // ================================================================

    /**
     * The patient this entry belongs to.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * The clinic service this entry is for.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * The staff member serving this patient.
     */
    public function staffMember(): BelongsTo
    {
        return $this->belongsTo(User::class, 'served_by');
    }

    // ================================================================
    // Query Scopes
    // ================================================================

    /**
     * Scope: entries with WAITING status.
     */
    public function scopeWaiting($query)
    {
        return $query->where('status', self::STATUS_WAITING);
    }

    /**
     * Scope: entries with CALLED status.
     */
    public function scopeCalled($query)
    {
        return $query->where('status', self::STATUS_CALLED);
    }

    /**
     * Scope: entries currently being served.
     */
    public function scopeInService($query)
    {
        return $query->where('status', self::STATUS_IN_SERVICE);
    }

    /**
     * Scope: active entries (not completed or cancelled).
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', self::ACTIVE_STATUSES);
    }

    /**
     * Scope: today's entries.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope: entries for a specific service.
     */
    public function scopeForService($query, int $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    /**
     * Scope: order by priority (URGENT first) then sequence.
     */
    public function scopeByQueueOrder($query)
    {
        return $query->orderByRaw("CASE priority WHEN 'URGENT' THEN 0 ELSE 1 END")
                     ->orderBy('sequence_number', 'asc');
    }

    // ================================================================
    // State Machine Methods
    // ================================================================

    /**
     * Check if a given status transition is valid.
     */
    public function canTransitionTo(string $newStatus): bool
    {
        return in_array($newStatus, self::VALID_TRANSITIONS[$this->status] ?? []);
    }

    /**
     * Check if this entry is in an active state.
     */
    public function isActive(): bool
    {
        return in_array($this->status, self::ACTIVE_STATUSES);
    }

    /**
     * Check if this entry is in a terminal state.
     */
    public function isTerminal(): bool
    {
        return in_array($this->status, self::TERMINAL_STATUSES);
    }

    // ================================================================
    // Computed Attributes
    // ================================================================

    /**
     * Get a human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'WAITING'    => 'Waiting',
            'CALLED'     => 'Called',
            'IN_SERVICE' => 'In Service',
            'COMPLETED'  => 'Completed',
            'CANCELLED'  => 'Cancelled',
            'SKIPPED'    => 'Skipped',
            default      => $this->status,
        };
    }

    /**
     * Get the CSS badge class for the current status.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'WAITING'    => 'badge-waiting',
            'CALLED'     => 'badge-called',
            'IN_SERVICE' => 'badge-in-service',
            'COMPLETED'  => 'badge-completed',
            'CANCELLED'  => 'badge-cancelled',
            'SKIPPED'    => 'badge-skipped',
            default      => 'badge-completed',
        };
    }

    /**
     * Calculate how long the patient waited (minutes) before service started.
     * Returns null if service hasn't started yet.
     */
    public function getWaitDurationMinutesAttribute(): ?int
    {
        if (!$this->service_started_at) {
            return null;
        }
        return (int) $this->joined_at->diffInMinutes($this->service_started_at);
    }
}
