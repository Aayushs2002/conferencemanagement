<?php

namespace App\Models\Template;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'conference_id',
        'key',
        'subject',
        'body'
    ];
}
