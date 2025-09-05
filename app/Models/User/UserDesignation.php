<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserDesignation extends Model
{
    protected $fillable = [
        'user_id',
        'designation_name'
    ];
}
