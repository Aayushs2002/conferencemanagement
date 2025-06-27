<?php

namespace App\Models\Sponsor;

use Illuminate\Database\Eloquent\Model;

class SponsorAttendance extends Model
{
    protected $fillable = [
        'sponsor_id',
        'status'
    ];

    public function sponsor()
    {
        return $this->belongsTo(sponsor::class);
    }
}
