<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class ConferenceSetting extends Model
{
    protected $fillable = [
        'conference_id',
        'name',
        'signature',
        'registration_guideline',
        'registration_guideline_youtube',
        'submission_guideline_youtube',
        'expert_guideline_youtube',
        'logo_display_type'
    ];
}
 