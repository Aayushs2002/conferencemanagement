<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class SubmissionRating extends Model
{
    protected $fillable = [
        'submission_id',
        'introduction',
        'method',
        'result',
        'conclusion',
        'grammar',
        'overall_rating',
        'status'
    ];
}
