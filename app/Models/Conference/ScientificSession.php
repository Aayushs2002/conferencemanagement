<?php

namespace App\Models\Conference;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ScientificSession extends Model
{
    protected $fillable = [
        'conference_id',
        'submission_id',
        'is_from_submission',
        'day',
        'topic',
        'hall_id',
        'start_time',
        'end_time',
        'scientific_session_category_id',
        'session_chair_id',
        'presenter_id',
        'description',
        'status',
    ];


    public function category()
    {
        return $this->belongsTo(ScientificSessionCategory::class, 'scientific_session_category_id', 'id');
    }

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    public function sessionChair()
    {
        return $this->belongsTo(User::class, 'session_chair_id', 'id');
    }

    public function submission()
    {
        return $this->belongsTo(Submission::class, 'submission_id', 'id');
    }
}
