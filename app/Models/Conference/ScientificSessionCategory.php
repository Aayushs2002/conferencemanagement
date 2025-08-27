<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class ScientificSessionCategory extends Model
{
    protected $fillable = [
        'conference_id',
        'category_name',
        'parent_id',
        'slug',
        'status'
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class, 'conference_id', 'id');
    }
}
