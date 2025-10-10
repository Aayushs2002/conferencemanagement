<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserDepartment extends Model
{
    protected $fillable = [
        'user_id',
        'department_name'
    ];

    public function user()
    {
       return $this->belongsTo(User::class);
    }
}
