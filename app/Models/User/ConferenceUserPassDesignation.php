<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class ConferenceUserPassDesignation extends Model
{
    protected $fillable = [
        'conference_id',
        'user_id',
        'pass_designation',
    ];
}
