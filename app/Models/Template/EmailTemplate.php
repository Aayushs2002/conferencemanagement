<?php

namespace App\Models\Template;

use App\Models\Conference\Conference;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'conference_id',
        'key',
        'subject',
        'body'
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class, 'conference_id', 'id');
    }
}
