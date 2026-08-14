<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bed extends Model
{
    use HasFactory;

    protected $fillable = [
        'ward_name',
        'bed_number',
        'bed_type',
        'status',
        'current_patient_id',
        'notes',
    ];

    public const STATUS_AVAILABLE   = 'AVAILABLE';
    public const STATUS_OCCUPIED    = 'OCCUPIED';
    public const STATUS_MAINTENANCE = 'MAINTENANCE';
    public const STATUS_RESERVED    = 'RESERVED';

    public const TYPE_ICU          = 'ICU';
    public const TYPE_GENERAL      = 'GENERAL';
    public const TYPE_OBSERVATION  = 'OBSERVATION';
    public const TYPE_TRIAGE_BAY   = 'TRIAGE_BAY';

    public function currentPatient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_patient_id');
    }

    public function queueEntries(): HasMany
    {
        return $this->hasMany(QueueEntry::class, 'allocated_bed_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_AVAILABLE   => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            self::STATUS_OCCUPIED    => 'bg-rose-100 text-rose-800 border-rose-300',
            self::STATUS_RESERVED    => 'bg-amber-100 text-amber-800 border-amber-300',
            self::STATUS_MAINTENANCE => 'bg-slate-100 text-slate-700 border-slate-300',
            default                  => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }
}
