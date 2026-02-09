<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConferenceMemberTypeNameTag extends Model
{
    protected $fillable = [
        'conference_id',
        'member_type_id',
        'registrant_type',
        'name_tag',
        'color',
        'status'
    ];
}
