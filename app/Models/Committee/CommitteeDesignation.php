<?php

namespace App\Models\Committee;

use Illuminate\Database\Eloquent\Model;

class CommitteeDesignation extends Model
{
    protected $fillable = [
        'society_id',
        'designation',
        'order_no',
        'status'
    ];

    // public function committeeMembers()
    // {
    //     return $this->hasMany(CommitteeMember::class, 'designation_id', 'id');
    // }
}
