<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserInstitution extends Model
{
    protected $fillable = [
        'user_id',
        'institution_name'
    ];
}
