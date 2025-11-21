<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'name',
        'status'
    ];

    public function societies()
    {
        return $this->belongsToMany(Society::class, 'society_department');
    }
}
