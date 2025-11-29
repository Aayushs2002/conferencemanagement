<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class Hall extends Model
{
    protected $fillable = [
        'conference_id',
        'hall_name',
        'status'
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class, 'conference_id', 'id');
    }

    public function scientificSessions()
    {
        return $this->hasMany(ScientificSession::class, 'hall_id');
    }
}
