<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class NamePrefix extends Model
{
    protected $fillable = [
        'prefix',
        'status'
    ];
}
