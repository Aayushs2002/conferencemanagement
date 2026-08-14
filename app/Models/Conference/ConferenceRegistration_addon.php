<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class ConferenceRegistration_addon extends Model
{
    protected $fillable = [
        'conference_registration_id',
        'conference_addon_id',
        'amount',
        // Was missing: the column exists (2025_12_09_115148) and the participant
        // flow writes it via a raw DB::table insert, so mass assignment silently
        // dropped it for anything going through the model.
        'include_for_guests',
        'status',
    ];

    protected $casts = [
        'include_for_guests' => 'boolean',
    ];

    public function ConferenceAddon()
    {
        return $this->belongsTo(ConferenceAddon::class, 'conference_addon_id');
    }
}
