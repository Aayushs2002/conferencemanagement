<?php

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'name',
        'image',
        'designation',
        'organization_name',
        'description',
        'rating',
        'status'
    ];
}
