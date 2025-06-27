<?php

namespace App\Models\Conference;

use App\Models\SubmissionSetting;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class Conference extends Model
{
    protected $fillable = [
        'society_id',
        'conference_name',
        'conference_theme',
        'conference_logo',
        'start_date',
        'end_date',
        'start_time',
        'regular_registration_deadline',
        'early_bird_registration_deadline',
        'conference_description',
        'primary_color',
        'secendary_color',
        'slug',
        'status'
    ];


    public function getRouteKey()
    {
        return Hashids::encode($this->attributes['id']);
    }

    public static function findByHashid($hashid)
    {
        $id = Hashids::decode($hashid)[0] ?? null;
        return static::findOrFail($id);
    }

    public function ConferenceVenueDetail()
    {
        return $this->hasOne(ConferenceVenueDetail::class, 'conference_id', 'id');
    }

    public function ConferenceOrganizer()
    {
        return $this->hasOne(ConferenceOrganizer::class, 'conference_id', 'id');
    }
    
    public function submissionSetting()
    {
        return $this->hasOne(SubmissionSetting::class);
    }
}
