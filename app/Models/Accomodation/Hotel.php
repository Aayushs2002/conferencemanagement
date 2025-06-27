<?php

namespace App\Models\Accomodation;

use App\Models\Conference\Conference;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $fillable = [
        'conference_id',
        'name',
        'address',
        'phone',
        'email',
        'featured_image',
        'cover_image',
        'images',
        'rating',
        'google_map',
        'description',
        'visible_status',
        'status',
        'slug',
        'contact_person',
        'price',
        'website',
        'facility',
        'promo_code'
    ];

    protected $casts = [
        'images' => 'array'
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
