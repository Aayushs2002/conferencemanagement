<?php

namespace App\Http\Controllers\Backend\Conference;

use App\Http\Controllers\Controller;
use App\Models\Accomodation\Hotel;
use App\Models\Accomodation\InternationalAccommodation;
use App\Models\Conference\Conference;
use App\Models\User\Country;
use App\Models\User\Society;
use Illuminate\Http\Request;

class AccommodationManagementController extends Controller
{
    public function index(Society $society, Conference $conference)
    {
        $accommodations = InternationalAccommodation::query()
            ->whereHas('conferenceRegistration', function($query) use ($conference) {
                $query->where('conference_id', $conference->id);
            })
            ->with([
                'conferenceRegistration.user.userDetail',
                'hotel'
            ])
            ->get();

        $countries = Country::orderBy('country_name')->get();
        $hotels = Hotel::orderBy('name')->get();

        return view('backend.conference.accommodation.index', compact('society', 'conference', 'accommodations', 'countries', 'hotels'));
    }

    public function show(Society $society, Conference $conference, InternationalAccommodation $accommodation)
    {
        $accommodation->load([
            'conferenceRegistration.user.userDetail',
            'hotel'
        ]);

        return view('backend.conference.accommodation.show', compact('society', 'conference', 'accommodation'));
    }
}