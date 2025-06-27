<?php

namespace App\Models\Conference;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Expert extends Model
{
    protected $fillable = [
        'user_id',
        'conference_id',
        'status'
    ];

    public function expert()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
