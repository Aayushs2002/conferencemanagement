<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'country_name',
        'status'
    ];
}
