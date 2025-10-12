<?php

namespace App\Http\Controllers\Frontend\MainPage;

use App\Http\Controllers\Controller;
use App\Models\Accomodation\Hotel;
use App\Models\Accomodation\InternationalAccommodation;
use App\Models\Conference\Conference;
use App\Models\User\Society;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccommodationController extends Controller
{
    public function index(Society $society, Conference $conference)
    {
        $conferenceRegistration = Auth::user()->conferenceRegistrations()
            ->where('conference_id', $conference->id)
            ->first();

        $accommodation = null;
        if ($conferenceRegistration) {
            $accommodation = InternationalAccommodation::where('user_id', Auth::id())
                ->where('conference_registration_id', $conferenceRegistration->id)
                ->first();
        }
        // @dd($accommodation);

        $hotels = Hotel::where('conference_id', $conference->id)
            ->where('status', 1)
            ->get();

        return view('frontend.main-page.conference.accommodation', compact('society', 'conference', 'accommodation', 'hotels'));
    }

    public function store(Request $request, Society $society, Conference $conference)
    {
        $validated = $request->validate([
            'flight_number' => 'required|string|max:50',
            'arrival_date' => 'required|date',
            'arrival_time' => 'required',
            'departure_date' => 'required|date|after_or_equal:arrival_date',
            'departure_time' => 'required',
            'hotel_id' => 'required|exists:hotels,id',
            // 'room_type' => 'required|in:single,double,suite',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after_or_equal:check_in_date',
            // 'airport_pickup_required' => 'boolean',
            // 'special_requirements' => 'nullable|string|max:1000',
        ]);

        $conferenceRegistration = Auth::user()->conferenceRegistrations()
            ->where('conference_id', $conference->id)
            ->firstOrFail();

        InternationalAccommodation::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'conference_registration_id' => $conferenceRegistration->id,
            ],
            [
                'hotel_id' => $validated['hotel_id'],
                'flight_number' => $validated['flight_number'],
                'arrival_date' => $validated['arrival_date'],
                'arrival_time' => $validated['arrival_time'],
                'departure_date' => $validated['departure_date'],
                'departure_time' => $validated['departure_time'],
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                // 'room_type' => $validated['room_type'],
                // 'airport_pickup_required' => $request->boolean('airport_pickup_required'),
                // 'special_requirements' => $validated['special_requirements'],
                'status' => true,
            ]
        );

        return redirect()
            ->back()
            ->with('status', 'Accommodation details have been saved successfully.');
    }
}