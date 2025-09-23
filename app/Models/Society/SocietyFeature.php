<?php

namespace App\Models\Society;

use Illuminate\Database\Eloquent\Model;

class SocietyFeature extends Model
{
    protected $fillable = [
        'society_id',
        'feature_id'
    ];
}
