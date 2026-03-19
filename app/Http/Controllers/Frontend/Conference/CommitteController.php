<?php

namespace App\Http\Controllers\Frontend\Conference;

use App\Http\Controllers\Controller;
use App\Models\Committee\Committee;
use App\Models\Conference\ConferenceSetting;
use Illuminate\Http\Request;

class CommitteController extends BaseConferenceController
{
    public function index()
    {
        // Check if static page is enabled
        $conferenceSetting = ConferenceSetting::where('conference_id', $this->conference->id)->first();
        $staticPageEnabled = $conferenceSetting?->committee_static_page_enabled ?? false;
        $staticContent = $conferenceSetting?->committee_static_page_content;

        // Get dynamic committee list
        $committees = Committee::with([
            'committeeMembers' => function ($query) {
                $query->where('status', 1)
                    ->where('conference_id', $this->conference->id)
                    ->orderBy('order', 'asc');
            },
            'committeeMembers.user.userDetail',
            'committeeMembers.designation'
        ])
            ->where('status', 1)
            ->orderBy('display_order', 'asc')
            ->where('society_id', $this->conference->society_id)
            ->get();

        return view('frontend.conference.about-conference.committe.index', compact('committees', 'staticPageEnabled', 'staticContent'));
    }
}
