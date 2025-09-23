<?php

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
        'title',
        'image',
        'slug',
        'description',
        'status'
    ];
}
