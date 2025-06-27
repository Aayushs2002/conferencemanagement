<?php

namespace App\Models\Workshop;

use Illuminate\Database\Eloquent\Model;

class WorkshopVenueDetail extends Model
{
    protected $fillable = [
        'workshop_id',
        'venue_name',
        'venue_address',
        'google_map_link',
    ];
}
