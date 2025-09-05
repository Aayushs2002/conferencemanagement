<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserDepartment extends Model
{
    protected $fillable = [
        'user_id',
        'department_name'
    ];
}
