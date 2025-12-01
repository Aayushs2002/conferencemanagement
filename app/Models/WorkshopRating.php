<?php

namespace App\Models;

use App\Models\Workshop\WorkshopRegistration;
use Illuminate\Database\Eloquent\Model;

class WorkshopRating extends Model
{
    protected $fillable = [
        'workshop_registration_id',
        'workshop_id',
        'user_id',
        'rating',
        'comment'
    ];

    public function registrant()
    {
        return $this->belongsTo(WorkshopRegistration::class);
    }

    public function workshop()
    {
        return $this->belongsTo(WorkshopRegistration::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
