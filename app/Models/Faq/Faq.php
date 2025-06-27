<?php

namespace App\Models\Faq;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = [
        'conference_id',
        'faq_category_id',
        'question',
        'answer',
        'order',
        'visible_status',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(faqCategory::class, 'faq_category_id', 'id');
    }
}
