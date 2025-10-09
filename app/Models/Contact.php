<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'full_name',
        'email',
        'contact_number', 
        'conference_type',
        'start_date',
        'end_date',
        'no_of_national_participant',
        'no_of_international_participant',
        'query'
    ];
}
