<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Conference\Conference;
use App\Models\User;

class ConferencePaymentStatus extends Model
{
    protected $fillable = [
        'conference_id',
        'user_id',
        'payment_status',
        'payment_method',
        'amount',
        'currency',
        'transaction_id',
        'payment_response',
        'error_message',
        'payment_initiated_at',
        'payment_completed_at'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_INCOMPLETE = 'incomplete';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    protected $casts = [
        'payment_initiated_at' => 'datetime',
        'payment_completed_at' => 'datetime'
    ];

    /**
     * Relationship with Conference
     */
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return [
            self::STATUS_PENDING => 'warning',
            self::STATUS_INCOMPLETE => 'secondary',
            self::STATUS_PROCESSING => 'info',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_FAILED => 'danger',
            self::STATUS_CANCELLED => 'dark'
        ][$this->payment_status] ?? 'secondary';
    }
}
