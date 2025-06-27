<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class ConferenceOrganizer extends Model
{
    protected $fillable = [
        'conference_id',
        'organizer_name',
        'organizer_email',
        'organizer_phone_number',
        'organizer_logo',
        'organizer_contact_person',
        'organizer_description',
        'status'
    ];
}
