<?php

namespace App\Http\Controllers\Frontend\MainPage;

use App\Http\Controllers\Controller;
use App\Models\Conference\Conference;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ConferenceController extends Controller
{
    public function index(Request $request)
    {
        // dd($request);
        $conferences = Conference::where('status', 1)
            // ->whereDate('end_date', '>=', Carbon::now())
            ->orderBy('end_date', 'desc')
            ->get();
        $query = Conference::with(['society', 'ConferenceVenueDetail'])
            ->where('status', 1); 

        if ($request->filled('search')) {
            $query->where('conference_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('organization') && $request->organization != '') {
            $query->where('society_id', $request->organization);
        }

        if ($request->filled('tags') && $request->tags != '') {
            $query->where('tags', 'like', '%' . $request->tags . '%');
        }

        if ($request->filled('month') && $request->month != '') {
            $monthNumber = date('m', strtotime($request->month . ' 1'));
            $query->whereMonth('start_date', $monthNumber);
        }
        $conferences = $query->orderBy('start_date', 'asc')->get();
        return view('frontend.main-page.conference.index', compact('conferences'));
    }

    public function filter(Request $request)
    {
        $query = Conference::with(['society', 'ConferenceVenueDetail'])
            ->where('status', 1);

        if ($request->filled('search')) {
            $query->where('conference_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('organization') && $request->organization != '') {
            $query->where('society_id', $request->organization);
        }

        if ($request->filled('tags') && $request->tags != '') {
            $query->where('tags', 'like', '%' . $request->tags . '%');
        }

        if ($request->filled('month') && $request->month != '') {
            $monthNumber = date('m', strtotime($request->month . ' 1'));
            $query->whereMonth('start_date', $monthNumber);
        }

        $conferences = $query->orderBy('start_date', 'asc')->get();

        return view('frontend.main-page.conference.partials.conference-cards', compact('conferences'))->render();
    }
}
