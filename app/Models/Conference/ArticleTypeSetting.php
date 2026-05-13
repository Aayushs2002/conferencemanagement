<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class ArticleTypeSetting extends Model
{
    protected $fillable = [
        'article_type_id',
        'number_of_sections',
        'sections',
        'total_marks',
        'scoring_allowed',
        'title_scoring_enabled',
        'title_max_marks',
        'title_reviewer_instruction',
        'overall_instruction',
        'attachment_name',
        'is_attachment_required',
        'author_limit',
        'is_conflict_of_interest_required',
        'is_source_of_funding_required',
        'allowed_member_type_ids',
        'status'
    ];

    protected $casts = [
        'sections' => 'array',
        'is_attachment_required' => 'boolean',
        'scoring_allowed' => 'boolean',
        'title_scoring_enabled' => 'boolean',
        'is_conflict_of_interest_required' => 'boolean',
        'is_source_of_funding_required' => 'boolean',
        'allowed_member_type_ids' => 'array',
    ];

    public function articleType()
    {
        return $this->belongsTo(ArticleType::class, 'article_type_id', 'id');
    }
}
