<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class PassSetting extends Model
{
    protected $fillable = [
        'conference_id',
        'image',
        'lunch_start_time',
        'lunch_end_time',
        'dinner_start_time',
        'dinner_end_time',
        'workshop_participant_name_tag',
        'workshop_participant_color',
        'workshop_trainer_name_tag',
        'workshop_trainer_color',
        'status' 
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
