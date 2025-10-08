<?php

namespace App\Http\Controllers\Frontend\Conference;

use App\Http\Controllers\Controller;
use App\Models\Conference\Hall;
use App\Models\Conference\ScientificSession;
use Illuminate\Http\Request;

class ScientificSessionController extends BaseConferenceController
{
    public function index()
    {
        $halls = Hall::where('conference_id', $this->conference->id)
            ->where('status', 1)
            ->get();

        $sessions = ScientificSession::with(['hall', 'category', 'submission', 'sessionChair'])
            ->where('conference_id', $this->conference->id)
            ->where('status', 1)
            ->orderBy('day')
            ->orderBy('start_time')
            ->get()
            ->groupBy(['day', 'hall_id']); // Group by day and hall
        // dd($sessions);
        $days = [];
        $start = \Carbon\Carbon::parse($this->conference->start_date);
        $end = \Carbon\Carbon::parse($this->conference->end_date);
        for ($date = $start; $date->lte($end); $date->addDay()) {
            $days[] = $date->copy();
        }
        return view('frontend.conference.scientific-session.index', compact('halls', 'sessions', 'days'));
    }
}
