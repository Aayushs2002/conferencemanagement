<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class MemberType extends Model
{
    protected $fillable = [
        'society_id',
        'delegate',
        'type',
        'status'
    ];
}
