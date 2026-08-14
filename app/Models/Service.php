<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'prefix',
        'avg_duration_minutes',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'avg_duration_minutes' => 'integer',
        ];
    }

    // ================================================================
    // Relationships
    // ================================================================

    /**
     * All queue entries for this service.
     */
    public function queueEntries(): HasMany
    {
        return $this->hasMany(QueueEntry::class);
    }

    /**
     * Today's active queue entries (WAITING, CALLED, IN_SERVICE).
     */
    public function activeQueueEntries(): HasMany
    {
        return $this->hasMany(QueueEntry::class)
                    ->whereDate('created_at', today())
                    ->whereIn('status', ['WAITING', 'CALLED', 'IN_SERVICE']);
    }

    // ================================================================
    // Query Scopes
    // ================================================================

    /**
     * Scope: only active services.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ================================================================
    // Business Logic Methods
    // ================================================================

    /**
     * Get the current waiting count for this service.
     */
    public function getWaitingCountAttribute(): int
    {
        return $this->queueEntries()
                    ->whereDate('created_at', today())
                    ->where('status', 'WAITING')
                    ->count();
    }

    /**
     * Get the number of patients completed today.
     */
    public function getCompletedTodayAttribute(): int
    {
        return $this->queueEntries()
                    ->whereDate('created_at', today())
                    ->where('status', 'COMPLETED')
                    ->count();
    }

    /**
     * Get the currently being served entry for this service.
     */
    public function getCurrentlyServingAttribute(): ?QueueEntry
    {
        return $this->queueEntries()
                    ->whereDate('created_at', today())
                    ->where('status', 'IN_SERVICE')
                    ->first();
    }

    /**
     * Get the last called entry for display on dashboards.
     */
    public function getLastCalledAttribute(): ?QueueEntry
    {
        return $this->queueEntries()
                    ->whereDate('created_at', today())
                    ->whereIn('status', ['CALLED', 'IN_SERVICE'])
                    ->latest('called_at')
                    ->first();
    }

    /**
     * Check if service can accept new queue entries.
     */
    public function canAcceptQueue(): bool
    {
        return $this->is_active;
    }
}
