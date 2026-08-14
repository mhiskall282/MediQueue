<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicalMessage extends Model
{
    use HasFactory;

    public const URGENCY_ROUTINE        = 'ROUTINE';
    public const URGENCY_URGENT         = 'URGENT';
    public const URGENCY_STAT_EMERGENCY = 'STAT_EMERGENCY';

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'queue_entry_id',
        'subject',
        'message',
        'urgency',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function queueEntry(): BelongsTo
    {
        return $this->belongsTo(QueueEntry::class, 'queue_entry_id');
    }

    public function getUrgencyBadgeClassAttribute(): string
    {
        return match($this->urgency) {
            self::URGENCY_STAT_EMERGENCY => 'bg-red-600 text-white animate-pulse',
            self::URGENCY_URGENT         => 'bg-orange-500 text-white',
            default                      => 'bg-indigo-100 text-indigo-800',
        };
    }
}
