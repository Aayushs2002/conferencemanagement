<?php

namespace App\Models\Sponsor;

use Illuminate\Database\Eloquent\Model;

class SponsorCategory extends Model
{
    protected $fillable = [
        'society_id',
        'category_name',
        'slug',
        'status'
    ];
}
