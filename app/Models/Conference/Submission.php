<?php

namespace App\Models\Conference;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class Submission extends Model
{
    protected $fillable = [
        'conference_id',
        'user_id',
        'expert_id',
        'submission_category_major_track_id',
        'title',
        'article_type',
        'presentation_type',
        'presentation_type_change',
        'keywords',
        'abstract_content',
        'submitted_date',
        'review_status',
        'request_status',
        'reject_remark',
        'status'
    ];

    // request status values
    // 0 => pending
    // 1 => accepted
    // 2 => correction
    // 3 => rejected

    // presentation_type_change value
    // 0 => sent to autor
    // 1=>accepted by author
    // 2=> rejected by author

    public function getRouteKey()
    {
        return Hashids::encode($this->attributes['id']);
    }

    public static function findByHashid($hashid)
    {
        $id = Hashids::decode($hashid)[0] ?? null;
        return static::findOrFail($id);
    }

    public function authors()
    {
        return $this->hasMany(Author::class);
    }

    public function presenter()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class, 'conference_id', 'id');
    }

    public function expert()
    {
        return $this->belongsTo(User::class, 'expert_id');
    }

    public function submissionRating()
    {
        return $this->hasOne(SubmissionRating::class);
    }

    public function discussions()
    {
        return $this->hasMany(SubmissionDiscussion::class);
    }

    public function submissionCategoryMajorTrack()
    {
        return $this->belongsTo(SubmissionCategoryMajorTrack::class, 'submission_category_major_track_id', 'id');
    }
}
