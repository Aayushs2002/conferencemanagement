<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class UserVote extends Model
{
    protected $fillable = [
        'conference_registration_id',
        'poll_id',
        'poll_answer_id'
    ];

    public function conferenceRegistration()
    {
        return $this->belongsTo(ConferenceRegistration::class);
    }

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function answer()
    {
        return $this->belongsTo(PollAnswer::class, 'poll_answer_id');
    }
}
