<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class ConferenceRegistration_addon extends Model
{
    protected $fillable = [
        'conference_registration_id',
        'conference_addon_id',
        'amount',
        'status'
    ];

    public function ConferenceAddon()
    {
        return $this->belongsTo(ConferenceAddon::class, 'conference_addon_id');
    }
}
