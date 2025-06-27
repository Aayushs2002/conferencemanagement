<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class PollAnswer extends Model
{
    protected $fillable = [
        'poll_id',
        'answer_text',
        'is_correct'
    ];
    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    // public function votes()
    // {
    //     return $this->hasMany(UserVote::class, 'answer_id', 'id');
    // }
}
