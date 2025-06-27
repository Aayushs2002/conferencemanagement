<?php

namespace App\Models\Faq;

use Illuminate\Database\Eloquent\Model;

class FaqCategory extends Model
{
    protected $fillable = [
        'society_id',
        'category_name',
        'slug',
        'status'
    ];
}
