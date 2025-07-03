<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class SubmissionCategoryMajorTrack extends Model
{

    protected $fillable = [
        'conference_id',
        'title',
        'major_areas',
        'status'
    ];

    public function conference()
    {
       return $this->belongsTo(Conference::class);
    }
}
