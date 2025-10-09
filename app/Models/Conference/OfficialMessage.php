<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class OfficialMessage extends Model
{
    protected $fillable = [
        'conference_id',
        'full_name',
        'designation',
        'image',
        'is_featured',
        'status'
    ];
}
