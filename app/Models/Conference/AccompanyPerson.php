<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class AccompanyPerson extends Model
{
    protected $fillable = [
        'conference_registration_id',
        'person_name',
        'status'
    ];
}
