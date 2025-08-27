<?php

namespace App\Models\Notice;

use App\Models\Conference\Conference;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $fillable = [
        'conference_id',
        'title',
        'date',
        'attachment',
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
