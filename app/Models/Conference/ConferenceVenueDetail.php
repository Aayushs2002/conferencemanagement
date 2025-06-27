<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class ConferenceVenueDetail extends Model
{
    protected $fillable = [
        'conference_id',
        'venue_name',
        'venue_address',
        'venue_contact_person_name',
        'venue_phone_number',
        'venue_email',
        'google_map_link',
        'status'
    ];
}
