<?php

namespace App\Models\Sponsor;

use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    protected $fillable = [
        'conference_id',
        'sponsor_category_id',
        'name',
        'amount',
        'logo',
        'flyers_ads',
        'address',
        'contact_person',
        'email',
        'phone',
        'description',
        'total_attendee',
        'visible_status',
        'lunch_access',
        'dinner_access',
        'status',
        'token',
    ];

    public function category()
    {
        return $this->belongsTo(SponsorCategory::class, 'sponsor_category_id', 'id');
    }

    public function attendances()
    {
        return $this->hasMany(SponsorAttendance::class, 'sponsor_id', 'id');
    }

    public function meals()
    {
        return $this->hasMany(SponsorMeal::class, 'sponsor_id', 'id');
    }
}
