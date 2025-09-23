<?php

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Model;

class WhyChooseUs extends Model
{
    protected $fillable = [
        'title',
        'image',
        'description',
        'status'
    ];
}
