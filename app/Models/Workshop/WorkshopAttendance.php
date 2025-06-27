<?php

namespace App\Models\Workshop;

use Illuminate\Database\Eloquent\Model;

class WorkshopAttendance extends Model
{
    protected $fillable = [
        'workshop_registration_id',
        'status'
    ];

    public function workshopRegistration()
    {
        return $this->belongsTo(WorkshopRegistration::class);
    }
}
