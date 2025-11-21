<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $fillable = [
        'designation',
        'status'
    ];

    public function societies()
    {
        return $this->belongsToMany(Society::class, 'society_designation');
    }
}
