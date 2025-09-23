<?php

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = [
        'key',
        'value'
    ];
}
