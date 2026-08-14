<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorRoster extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'duty_date',
        'shift_type',
        'status',
        'duty_notes',
    ];

    protected $casts = [
        'duty_date' => 'date',
    ];

    public const SHIFT_DAY            = 'DAY';
    public const SHIFT_NIGHT          = 'NIGHT';
    public const SHIFT_ON_CALL_TRAUMA = 'ON_CALL_TRAUMA';
    public const SHIFT_ICU_COVER      = 'ICU_COVER';

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('duty_date', Carbon::today());
    }

    public function scopeActiveOnCall($query)
    {
        return $query->where('status', 'ACTIVE')->orWhere(function ($q) {
            $q->whereDate('duty_date', Carbon::today())->where('status', 'SCHEDULED');
        });
    }

    public function getShiftLabelAttribute(): string
    {
        return match($this->shift_type) {
            self::SHIFT_DAY            => '☀️ Day Shift (08:00 - 18:00)',
            self::SHIFT_NIGHT          => '🌙 Night Emergency (18:00 - 08:00)',
            self::SHIFT_ON_CALL_TRAUMA => '🚨 24h Trauma On-Call',
            self::SHIFT_ICU_COVER      => '🏥 ICU Critical Cover',
            default                    => $this->shift_type,
        };
    }
}
