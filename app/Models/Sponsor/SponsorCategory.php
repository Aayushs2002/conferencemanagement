<?php

namespace App\Models\Sponsor;

use Illuminate\Database\Eloquent\Model;

class SponsorCategory extends Model
{
    protected $fillable = [
        'society_id',
        'category_name',
        'slug',
        'status',
        'display_order'
    ];

    public function sponsors()
    {
        return $this->hasMany(Sponsor::class, 'sponsor_category_id', 'id');
    }
}
