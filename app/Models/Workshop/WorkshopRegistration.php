<?php

namespace App\Models\Workshop;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class WorkshopRegistration extends Model
{
    protected $fillable = [
        'user_id',
        'workshop_id',
        'registrant_type',
        'transaction_id',
        'payment_voucher',
        'payment_type',
        'verified_status',
        'token',
        'remarks',
        'amount',
        'meal_type',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function attendances()
    {
        return $this->hasMany(WorkshopAttendance::class, 'workshop_registration_id', 'id');
    }
}
