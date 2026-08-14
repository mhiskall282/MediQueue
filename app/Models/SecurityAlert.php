<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityAlert extends Model
{
    use HasFactory;

    public const SEVERITY_LOW      = 'LOW';
    public const SEVERITY_MEDIUM   = 'MEDIUM';
    public const SEVERITY_HIGH     = 'HIGH';
    public const SEVERITY_CRITICAL = 'CRITICAL';

    protected $fillable = [
        'user_id',
        'event_type',
        'severity',
        'description',
        'context_data',
        'ip_address',
        'is_resolved',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'context_data' => 'array',
        'is_resolved'  => 'boolean',
        'resolved_at'  => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function getSeverityBadgeClassAttribute(): string
    {
        return match($this->severity) {
            self::SEVERITY_CRITICAL => 'bg-red-700 text-white font-black animate-pulse',
            self::SEVERITY_HIGH     => 'bg-rose-600 text-white font-bold',
            self::SEVERITY_MEDIUM   => 'bg-amber-500 text-white font-semibold',
            default                 => 'bg-slate-200 text-slate-800',
        };
    }
}
