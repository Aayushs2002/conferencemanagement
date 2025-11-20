<?php

namespace App\Models\Conference;

use App\Models\Accomodation\Hotel;
use App\Models\Download\Download;
use App\Models\SubmissionSetting;
use App\Models\User\Society;
use App\Models\Workshop\Workshop;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class Conference extends Model
{
    protected $fillable = [
        'society_id',
        'conference_name',
        'abbreviation',
        'conference_theme',
        'conference_logo',
        'conference_banner',
        'start_date',
        'end_date',
        'start_time',
        'regular_registration_deadline',
        'early_bird_registration_deadline',
        'conference_description',
        'primary_color',
        'secendary_color',
        'tags',
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

    public function society()
    {
        return $this->belongsTo(Society::class);
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

    public function conferenceCertificate()
    {
        return $this->hasOne(ConferenceCertificate::class);
    }

    public function hotels()
    {
        return $this->hasMany(Hotel::class, 'conference_id', 'id')->where('status', 1);
    }
    public function workshops()
    {
        return $this->hasMany(Workshop::class, 'conference_id', 'id');
    }

    public function downloads()
    {
        return $this->hasMany(Download::class, 'conference_id', 'id')->where('status', 1);
    }

    public function officialMessages()
    {
        return $this->hasMany(OfficialMessage::class, 'conference_id', 'id')->where('status', 1);
    }

    public function conferenceSetting()
    {
        return $this->hasOne(ConferenceSetting::class);
    }

    public function customCss()
    {
        return $this->hasMany(ConferenceCustomCss::class, 'conference_id', 'id');
    }

    public function getCustomCss($sectionName)
    {
        $css = $this->customCss()->where('section_name', $sectionName)->where('status', 1)->first();
        return $css?->custom_css ?? '';
    }
}
