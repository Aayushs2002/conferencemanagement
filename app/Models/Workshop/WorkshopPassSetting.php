<?php

namespace App\Models\Workshop;

use App\Models\Conference\Conference;
use Illuminate\Database\Eloquent\Model;

class WorkshopPassSetting extends Model
{
    protected $fillable = [
        'conference_id',
        'image',
        'lunch_start_time',
        'lunch_end_time',
        'dinner_start_time',
        'dinner_end_time',
        'status'
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class, 'conference_id', 'id');
    }
}
