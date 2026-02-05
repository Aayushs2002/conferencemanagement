<?php

namespace App\Models;

use App\Models\Committee\Committee;
use App\Models\Committee\CommitteeDesignation;
use App\Models\Conference\Conference;
use Illuminate\Database\Eloquent\Model;

class ConferenceCommitteePassDesignation extends Model
{
    protected $fillable = [
        'conference_id',
        'committee_id',
        'designation_id',
        'name_tag',
        'color',
        'status'
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class, 'conference_id', 'id');
    }

    public function committee()
    {
        return $this->belongsTo(Committee::class, 'committee_id', 'id');
    }

    public function designation()
    {
        return $this->belongsTo(CommitteeDesignation::class, 'designation_id', 'id');
    }
}
