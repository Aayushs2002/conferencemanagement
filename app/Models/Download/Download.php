<?php

namespace App\Models\Download;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    protected $fillable = [
        'conference_id',
        'title',
        'date',
        'file',
        'description',
        'image',
        'is_featured',
        'status'
    ];
}
