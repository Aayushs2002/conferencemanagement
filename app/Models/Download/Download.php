<?php

namespace App\Models\Download;

use App\Models\Conference\Conference;
use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    protected $fillable = [
        'conference_id',
        'title',
        'date',
        'file',
        'description',
        'image',
        'is_featured',
        'status'
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class, 'conference_id', 'id');
    }
}
