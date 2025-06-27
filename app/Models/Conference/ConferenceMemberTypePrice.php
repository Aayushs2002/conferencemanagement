<?php

namespace App\Models\Conference;

use App\Models\User\MemberType;
use Illuminate\Database\Eloquent\Model;

class ConferenceMemberTypePrice extends Model
{
    protected $fillable = [
        'conference_id',
        'member_type_id',
        'early_bird_amount',
        'regular_amount',
        'on_site_amount',
        'guest_amount',
        'status'
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function memberType()
    {
        return $this->belongsTo(MemberType::class);
    }
}
