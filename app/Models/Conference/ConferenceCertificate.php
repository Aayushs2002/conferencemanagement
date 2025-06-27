<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class ConferenceCertificate extends Model
{
    protected $fillable = [
        'conference_id',
        'background_image',
        'signature',
    ];

    protected $casts = [
        'signature' => 'array'
    ];
}
