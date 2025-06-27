<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'conference_registration_id',
        'status'
    ];

    public function conferenceRegistration()
    {
        return $this->belongsTo(ConferenceRegistration::class);
    }
}
