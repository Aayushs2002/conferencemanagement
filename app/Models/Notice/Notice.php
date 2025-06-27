<?php

namespace App\Models\Notice;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $fillable = [
        'conference_id',
        'title',
        'date',
        'attachment',
        'description',
        'image',
        'is_featured',
        'status'
    ];
}
