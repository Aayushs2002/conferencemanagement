<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class ConferenceAddon extends Model
{
    protected $fillable = [
        'conference_id',
        'addon_name',
        'addon_national_amount',
        'addon_international_amount',
        'status'
    ];
}
