<?php

namespace App\Models\Sponsor;

use Illuminate\Database\Eloquent\Model;

class SponsorMeal extends Model
{
    protected $fillable = [
        'sponsor_id',
        'lunch_taken',
        'dinner_taken'
    ];

    public function sponsor()
    {
        return $this->belongsTo(Sponsor::class);
    }
}
