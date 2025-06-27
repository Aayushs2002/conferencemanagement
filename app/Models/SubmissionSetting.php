<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionSetting extends Model
{
    protected $fillable = [
        'conference_id',
        'deadline',
        'abstract_word_limit',
        'key_word_limit',
        'authors_limit',
        'abstract_guidelines',
        'oral_guidelines',
        'poster_guidelines',
        'oral_reviewer_guide',
        'poster_reviewer_guide',
        'status'
    ];
}
