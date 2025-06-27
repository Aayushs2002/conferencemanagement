<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class Hall extends Model
{
    protected $fillable = [
        'conference_id',
        'hall_name',
        'status'
    ];
}
