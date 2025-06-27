<?php

namespace App\Models\Workshop;

use Illuminate\Database\Eloquent\Model;

class WorkshopTrainer extends Model
{
    protected $fillable = [
        'workshop_id',
        'name',
        'email',
        'image',
        'affiliation',
        'cv',
        'status'
    ];

    public function workshop()
    {
        $this->belongsTo(Workshop::class);
    }
}
