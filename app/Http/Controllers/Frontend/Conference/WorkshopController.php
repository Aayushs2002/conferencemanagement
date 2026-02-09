<?php

namespace App\Http\Controllers\Frontend\Conference;

use App\Http\Controllers\Controller;
use App\Models\Conference\Conference;
use App\Models\Workshop\Workshop;
use Illuminate\Http\Request;

class WorkshopController extends BaseConferenceController
{
    public function index()
    {
        return view('frontend.conference.workshop.index');
    }
 
    public function singlePage($conference, $workshop)
    {
        // dd($workshop); 
        $workshop = Workshop::with([
            'WorkshopVenueDetail',
            'registrations' => function ($query) {
                $query->where('registrant_type', 2)->where('status', 1);
            }
        ])->where('is_published', true)->where('slug', $workshop)->first();

        // dd($workshop);
        $relevantWorkshops = Workshop::where('id', '!=', $workshop->id)
            ->where('conference_id', $this->conference->id)
            ->where('approval_status', 'approved')
            ->where('is_published', true)
            ->where('status', 1)
            ->orderBy('display_order', 'ASC')
            ->latest()
            // ->take(3)
            ->get();
        // dd($relevantWorkshops);
        return view('frontend.conference.workshop.single-page', compact('workshop', 'relevantWorkshops'));
    }
}
