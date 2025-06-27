<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    protected $fillable = [
        'conference_registration_id',
        'lunch_taken',
        'dinner_taken'
    ];
    public function conferenceRegistration()
    {
        return $this->belongsTo(ConferenceRegistration::class);
    }
}
