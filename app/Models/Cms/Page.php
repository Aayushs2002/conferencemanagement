<?php

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'image',
        'meta_tag',
        'meta_description',
        'is_active',
        'status'
    ];

    // public function getRouteKeyName()
    // {
    //     return 'slug';
    // }
}
