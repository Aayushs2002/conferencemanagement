<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    protected $fillable = [
        'name',
        'status'
    ];
}
