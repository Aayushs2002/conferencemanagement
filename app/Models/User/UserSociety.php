<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserSociety extends Pivot
{
    protected $table = 'user_societies';
    
    protected $fillable = [
        'user_id',
        'society_id',
        'member_type_id',
        'status'
    ];

    public function memberType()
    {
        return $this->belongsTo(MemberType::class, 'member_type_id');
    }
}
