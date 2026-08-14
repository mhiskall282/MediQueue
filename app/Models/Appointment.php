<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'service_id',
        'doctor_id',
        'appointment_date',
        'time_slot',
        'symptoms_notes',
        'status',
        'generated_queue_entry_id',
    ];

    protected $casts = [
        'appointment_date' => 'date',
    ];

    public const STATUS_BOOKED     = 'BOOKED';
    public const STATUS_CHECKED_IN = 'CHECKED_IN';
    public const STATUS_CANCELLED  = 'CANCELLED';
    public const STATUS_COMPLETED  = 'COMPLETED';

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function queueEntry(): BelongsTo
    {
        return $this->belongsTo(QueueEntry::class, 'generated_queue_entry_id');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', Carbon::today())
                     ->where('status', self::STATUS_BOOKED);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('appointment_date', Carbon::today());
    }

    public function canCheckIn(): bool
    {
        return $this->status === self::STATUS_BOOKED && $this->appointment_date->isToday();
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_BOOKED     => 'bg-blue-100 text-blue-800 border-blue-200',
            self::STATUS_CHECKED_IN => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::STATUS_CANCELLED  => 'bg-rose-100 text-rose-800 border-rose-200',
            self::STATUS_COMPLETED  => 'bg-slate-100 text-slate-700 border-slate-200',
            default                 => 'bg-slate-100 text-slate-800 border-slate-200',
        };
    }
}
