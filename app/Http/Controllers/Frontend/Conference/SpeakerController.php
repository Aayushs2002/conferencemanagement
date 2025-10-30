<?php

namespace App\Http\Controllers\Frontend\Conference;

use App\Http\Controllers\Controller;
use App\Models\Conference\ConferenceRegistration;
use Illuminate\Http\Request;

class SpeakerController extends BaseConferenceController
{
    public function index()
    {
        $allSpeaker = ConferenceRegistration::with(['user.userDetail.country'])
            ->where([
                'conference_id' => $this->conference->id,
                'registrant_type' => 2, 
                'status' => 1
            ])
            ->get();


        $nationalSpeakers = $allSpeaker->filter(function ($speaker) {
            return optional($speaker->user->userDetail)->country_id == 125;
        });
 
        $internationalSpeakers = $allSpeaker->filter(function ($speaker) {
            return optional($speaker->user->userDetail)->country_id != 125;
        });

        return view('frontend.conference.about-conference.speaker.index', compact(
            'allSpeaker',
            'nationalSpeakers',
            'internationalSpeakers'
        ));
    }
}
