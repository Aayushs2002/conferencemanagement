<?php

namespace App\Models\Cms;

use App\Models\User\Society;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $fillable = [
        'title',
        'image',
        'slug',
        'description',
        'status'
    ];

    public function societies()
    {
        return $this->belongsToMany(Society::class, 'society_features');
    }
}
