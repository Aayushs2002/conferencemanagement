<?php

namespace App\Models\Committee;

use App\Models\Conference\Conference;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CommitteeMember extends Model
{
    protected $fillable = [
        'conference_id',
        'committee_id',
        'user_id',
        'designation_id',
        'is_featured',
        'message',
        'slug',
        'status'
    ];

    public function committee()
    {
        return $this->belongsTo(Committee::class, 'committee_id', 'id');
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class, 'conference_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function designation()
    {
        return $this->belongsTo(CommitteeDesignation::class, 'designation_id', 'id');
    }
}
