<?php

namespace App\Models\Faq;

use App\Models\Conference\Conference;
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
        return $this->belongsTo(FaqCategory::class, 'faq_category_id', 'id');
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class, 'conference_id', 'id');
    }
}
