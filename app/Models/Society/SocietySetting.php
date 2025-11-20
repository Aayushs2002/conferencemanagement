<?php

namespace App\Models\Society;

use Illuminate\Database\Eloquent\Model;

class SocietySetting extends Model
{
    protected $fillable = [
        'society_id',
        'member_type_api',
        'member_detail_api',
        'banner_title',
        'banner_subtitle',
        'status'
    ];
}
