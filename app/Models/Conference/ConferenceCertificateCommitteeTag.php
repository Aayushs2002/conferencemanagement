<?php

namespace App\Models\Conference;

use App\Models\Committee\Committee;
use App\Models\Committee\CommitteeDesignation;
use Illuminate\Database\Eloquent\Model;

class ConferenceCertificateCommitteeTag extends Model
{
    protected $table = 'conference_certificate_committee_tags';

    protected $fillable = [
        'conference_id',
        'committee_id',
        'designation_id',
        'name_tag',
        'participating_text',
    ];

    public function committee()
    {
        return $this->belongsTo(Committee::class, 'committee_id', 'id');
    }

    public function designation()
    {
        return $this->belongsTo(CommitteeDesignation::class, 'designation_id', 'id');
    }
}
