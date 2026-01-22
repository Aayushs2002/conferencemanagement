<?php

namespace App\Models\Committee;

use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    protected $fillable = [
        'society_id',
        'committee_name',
        'slug',
        'focal_person',
        'email',
        'phone',
        'description',
        'display_order',
        'status'
    ];

    // public function committeeMembers()
    // {
    //     return $this->hasMany(CommitteeMember::class, 'committee_id', 'id');
    // }

    public function committeeMembers()
    {
        return $this->hasMany(CommitteeMember::class, 'committee_id', 'id')
            ->with(['user', 'designation'])
            ->orderBy('order', 'asc');
    }
}
