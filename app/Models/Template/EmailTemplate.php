<?php

namespace App\Models\Template;

use App\Models\Conference\Conference;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'conference_id',
        'key',
        'subject',
        'body',
        'partner_filter',
        'article_type_filter',
        'presentation_type_filter',
    ];

    protected $casts = [
        'partner_filter'           => 'array',
        'article_type_filter'      => 'array',
        'presentation_type_filter' => 'array',
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class, 'conference_id', 'id');
    }
}
