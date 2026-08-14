<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class QueueEntry extends Model
{
    use HasFactory;

    /**
     * Queue entry statuses.
     */
    public const STATUS_WAITING    = 'WAITING';
    public const STATUS_CALLED     = 'CALLED';
    public const STATUS_IN_SERVICE = 'IN_SERVICE';
    public const STATUS_COMPLETED  = 'COMPLETED';
    public const STATUS_CANCELLED  = 'CANCELLED';
    public const STATUS_SKIPPED    = 'SKIPPED';

    /**
     * Queue priority levels.
     */
    public const PRIORITY_NORMAL = 'NORMAL';
    public const PRIORITY_URGENT = 'URGENT';

    /**
     * Multi-Level Emergency Triage Categories (Manchester / Emergency Triage Protocol)
     */
    public const TRIAGE_RED    = 'RED';      // Priority 1: Resuscitation / Immediate Emergency
    public const TRIAGE_ORANGE = 'ORANGE';   // Priority 2: Very Urgent (10 min target)
    public const TRIAGE_YELLOW = 'YELLOW';   // Priority 3: Urgent (60 min target)
    public const TRIAGE_GREEN  = 'GREEN';    // Priority 4: Standard Outpatient (120 min target)
    public const TRIAGE_BLUE   = 'BLUE';     // Priority 5: Non-Urgent (240 min target)

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
     */
    public const VALID_TRANSITIONS = [
        'WAITING'    => ['CALLED', 'CANCELLED'],
        'CALLED'     => ['IN_SERVICE', 'SKIPPED', 'CANCELLED'],
        'IN_SERVICE' => ['COMPLETED'],
        'SKIPPED'    => ['CALLED', 'CANCELLED'],
        'COMPLETED'  => [],   // Terminal
        'CANCELLED'  => [],   // Terminal
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
        'triage_level',
        'triage_notes',
        'allocated_bed_id',
        'joined_at',
        'called_at',
        'service_started_at',
        'completed_at',
        'cancelled_at',
        'skipped_at',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'joined_at'          => 'datetime',
        'called_at'          => 'datetime',
        'service_started_at' => 'datetime',
        'completed_at'       => 'datetime',
        'cancelled_at'       => 'datetime',
        'skipped_at'         => 'datetime',
        'sequence_number'    => 'integer',
    ];

    // ================================================================
    // Relationships
    // ================================================================

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function staffMember(): BelongsTo
    {
        return $this->belongsTo(User::class, 'served_by');
    }

    public function servedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'served_by');
    }

    public function allocatedBed(): BelongsTo
    {
        return $this->belongsTo(Bed::class, 'allocated_bed_id');
    }

    public function appointment(): HasOne
    {
        return $this->hasOne(Appointment::class, 'generated_queue_entry_id');
    }

    // ================================================================
    // Query Scopes
    // ================================================================

    public function scopeWaiting($query)
    {
        return $query->where('status', self::STATUS_WAITING);
    }

    public function scopeCalled($query)
    {
        return $query->where('status', self::STATUS_CALLED);
    }

    public function scopeInService($query)
    {
        return $query->where('status', self::STATUS_IN_SERVICE);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', self::ACTIVE_STATUSES);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeForService($query, int $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    /**
     * Scope: Order by Triage Severity (RED > ORANGE > YELLOW > GREEN > BLUE),
     * then Priority (URGENT > NORMAL), then Sequence Number.
     */
    public function scopeByQueueOrder($query)
    {
        return $query->orderByRaw("
            CASE triage_level
                WHEN 'RED' THEN 1
                WHEN 'ORANGE' THEN 2
                WHEN 'YELLOW' THEN 3
                WHEN 'GREEN' THEN 4
                WHEN 'BLUE' THEN 5
                ELSE 4
            END ASC
        ")
        ->orderByRaw("CASE priority WHEN 'URGENT' THEN 0 ELSE 1 END")
        ->orderBy('sequence_number', 'asc');
    }

    // ================================================================
    // State Machine Methods
    // ================================================================

    public function canTransitionTo(string $newStatus): bool
    {
        return in_array($newStatus, self::VALID_TRANSITIONS[$this->status] ?? []);
    }

    public function isActive(): bool
    {
        return in_array($this->status, self::ACTIVE_STATUSES);
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, self::TERMINAL_STATUSES);
    }

    // ================================================================
    // Computed Attributes & Triage Badges
    // ================================================================

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

    public function getTriageLabelAttribute(): string
    {
        return match($this->triage_level) {
            self::TRIAGE_RED    => '🔴 Red — Immediate Emergency (P1)',
            self::TRIAGE_ORANGE => '🟠 Orange — Very Urgent (P2)',
            self::TRIAGE_YELLOW => '🟡 Yellow — Urgent (P3)',
            self::TRIAGE_GREEN  => '🟢 Green — Standard (P4)',
            self::TRIAGE_BLUE   => '🔵 Blue — Non-Urgent (P5)',
            default             => '🟢 Green — Standard (P4)',
        };
    }

    public function getTriageBadgeClassAttribute(): string
    {
        return match($this->triage_level) {
            self::TRIAGE_RED    => 'bg-red-500 text-white font-black animate-pulse border-red-600',
            self::TRIAGE_ORANGE => 'bg-orange-500 text-white font-bold border-orange-600',
            self::TRIAGE_YELLOW => 'bg-amber-400 text-amber-950 font-bold border-amber-500',
            self::TRIAGE_GREEN  => 'bg-emerald-500 text-white font-semibold border-emerald-600',
            self::TRIAGE_BLUE   => 'bg-sky-500 text-white font-semibold border-sky-600',
            default             => 'bg-emerald-500 text-white font-semibold border-emerald-600',
        };
    }

    public function getWaitDurationMinutesAttribute(): ?int
    {
        if (!$this->called_at && !$this->service_started_at) {
            return null;
        }
        $targetTime = $this->called_at ?? $this->service_started_at;
        return (int) $this->joined_at->diffInMinutes($targetTime);
    }

    public function getServiceDurationMinutesAttribute(): ?int
    {
        if (!$this->service_started_at || !$this->completed_at) {
            return null;
        }
        return (int) $this->service_started_at->diffInMinutes($this->completed_at);
    }
}
