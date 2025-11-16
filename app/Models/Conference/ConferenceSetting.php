<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class ConferenceSetting extends Model
{
    protected $fillable = [
        'conference_id',
        'name',
        'signature',
        'registration_guideline'
    ];
}
 