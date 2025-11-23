<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class ArticleTypeSetting extends Model
{
    protected $fillable = [
        'article_type_id',
        'number_of_sections',
        'sections',
        'attachment_name',
        'is_attachment_required',
        'author_limit',
        'is_conflict_of_interest_required',
        'is_source_of_funding_required',
        'status'
    ];

    protected $casts = [
        'sections' => 'array',
        'is_attachment_required' => 'boolean',
        'is_conflict_of_interest_required' => 'boolean',
        'is_source_of_funding_required' => 'boolean',
    ];

    public function articleType()
    {
        return $this->belongsTo(ArticleType::class, 'article_type_id', 'id');
    }
}
