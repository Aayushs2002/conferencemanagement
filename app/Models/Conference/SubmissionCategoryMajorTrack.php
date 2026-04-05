<?php

namespace App\Models\Conference;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class SubmissionCategoryMajorTrack extends Model
{

    protected $fillable = [
        'conference_id',
        'title',
        'major_areas',
        'status'
    ];

    public function conference()
    {
       return $this->belongsTo(Conference::class);
    }

    public function managers()
    {
        return $this->belongsToMany(User::class, 'submission_category_major_track_user', 'submission_category_major_track_id', 'user_id')
            ->withTimestamps();
    }

    public function isManagedBy(int $userId): bool
    {
        return $this->managers()->where('users.id', $userId)->exists();
    }
}
