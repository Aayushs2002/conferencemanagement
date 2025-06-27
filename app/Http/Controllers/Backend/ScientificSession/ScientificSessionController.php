<?php

namespace App\Http\Controllers\Backend\ScientificSession;

use App\Http\Controllers\Controller;
use App\Http\Requests\Conference\ScientificSessionRequest;
use App\Models\Conference\Hall;
use App\Models\Conference\ScientificSession;
use App\Models\Conference\ScientificSessionCategory;
use App\Models\Conference\Submission;
use App\Models\User;
use App\Models\User\Society;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class ScientificSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($society, $conference)
    {
        // $conferenceDetail = conference_detail();

        // if (empty($conferenceDetail)) {
        //     return redirect()->route('dashboard');
        // }
        $scientific_sessions = ScientificSession::where(['conference_id' => $conference->id, 'status' => 1])->get();

        $startDate = Carbon::parse($conference->start_date);
        $endDate = Carbon::parse($conference->end_date);

        $dates = [];

        while ($startDate->lte($endDate)) {
            $dates[] = $startDate->toDateString();
            $startDate->addDay();
        }

        return view('backend.schedule-plan.scientific-session.index', compact('scientific_sessions', 'dates', 'society', 'conference'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($society, $conference)
    {
        $startDate = Carbon::parse($conference->start_date);
        $endDate = Carbon::parse($conference->end_date);

        $dates = [];
        while ($startDate->lte($endDate)) {
            $dates[] = $startDate->toDateString();
            $startDate->addDay();
        }

        $halls = Hall::where(['status' => 1, 'conference_id' => $conference->id])->get();

        $society = Society::with(['users' => function ($query) {
            $query->where('type', 3)->orderByDesc('id');
        }])->where([
            'id' => $conference->society_id,
            'status' => 1
        ])->first();

        $users = $society ? $society->users : collect();

        $categories = ScientificSessionCategory::where(function ($query) use ($conference) {
            $query->where('status', 1)
                ->where(function ($q) use ($conference) {
                    $q->whereNull('conference_id')
                        ->orWhere('conference_id', $conference->id);
                });
        })->get();

        $submittedUserIds = Submission::where('conference_id', $conference->id)
            ->where('request_status', 1)
            ->where('status', 1)
            ->pluck('user_id')
            ->toArray();

        $presenters = $users->filter(fn($user) => in_array($user->id, $submittedUserIds))->values();

        $sessions = ScientificSession::where('conference_id', $conference->id)
            ->select('id', 'day', 'hall_id', 'start_time', 'end_time')
            ->get()
            ->groupBy(fn($session) => $session->day . '-' . $session->hall_id);

        $submittedPresentations = Submission::where('conference_id', $conference->id)
            ->where('request_status', 1)
            ->where('status', 1)->get();

        return view('backend.schedule-plan.scientific-session.create', compact(
            'dates',
            'halls',
            'users',
            'categories',
            'presenters',
            'society',
            'conference',
            'sessions',
            'submittedPresentations'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ScientificSessionRequest $request, $society, $conference)
    {
        try {
            // dd('s');
            $req = $request->all();


            $req['conference_id'] = $conference->id;
            $checkAvailabilityOfTime = ScientificSession::where(['conference_id' => $conference->id, 'day' => $request->day, 'start_time' => $request->start_time, 'hall_id' => $request->hall_id, 'status' => 1])->first();
            if (empty($checkAvailabilityOfTime)) {
                ScientificSession::create($req);
                return redirect()->route('scientific-session.index', [$society, $conference])->with('status', 'Scientifc Session Added Successfully');
            } else {
                return redirect()->back()->withInput()->with('delete', 'Time Slot Already Consumed For This Hall.');
            }
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('delete', 'internal server error');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($society, $conference, ScientificSession $scientific_session)
    {


        $startDate = Carbon::parse($conference->start_date);
        $endDate = Carbon::parse($conference->end_date);

        $dates = [];

        while ($startDate->lte($endDate)) {
            $dates[] = $startDate->toDateString();
            $startDate->addDay();
        }
        $halls = Hall::where(['status' => 1, 'conference_id' => $conference->id])->get();

        $society = Society::with(['users' => function ($query) {
            $query->where('type', 3)->orderByDesc('id');
        }])->where([
            'id' => $conference->society_id,
            'status' => 1
        ])->first();

        $users = $society ? $society->users : collect();

        $categories = ScientificSessionCategory::where(function ($query) use ($conference) {
            $query->where('status', 1)
                ->where(function ($q) use ($conference) {
                    $q->whereNull('conference_id')
                        ->orWhere('conference_id', $conference->id);
                });
        })->get();

        $submittedUserIds = Submission::where('conference_id', $conference->id)
            ->where('request_status', 1)
            ->where('status', 1)
            ->pluck('user_id')
            ->toArray();

        $presenters = $users->filter(fn($user) => in_array($user->id, $submittedUserIds))->values();

        $submittedPresentations = Submission::where('conference_id', $conference->id)
            ->where('request_status', 1)
            ->where('status', 1)->get();

        $sessions = ScientificSession::where('conference_id', $conference->id)
            ->select('id', 'day', 'hall_id', 'start_time', 'end_time')
            ->get()
            ->groupBy(fn($session) => $session->day . '-' . $session->hall_id);
        return view('backend.schedule-plan.scientific-session.create', compact('dates', 'halls', 'users', 'categories', 'presenters', 'scientific_session', 'society', 'conference', 'sessions', 'submittedPresentations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ScientificSessionRequest $request, $society, $conference, ScientificSession $scientific_session)
    {
        try {
            $req = $request->all();

            $checkAvailabilityOfTime = ScientificSession::where(['conference_id' => $conference->id, 'day' => $request->day, 'start_time' => $request->start_time, 'hall_id' => $request->hall_id, 'status' => 1])->whereNot('id', $scientific_session->id)->first();

            if (empty($checkAvailabilityOfTime)) {
                $scientific_session->update($req);
                return redirect()->route('scientific-session.index', [$society, $conference])->with('status', 'Scientifc Session Added Successfully');
            } else {
                return redirect()->back()->withInput()->with('delete', 'Time Slot Already Consumed For This Hall.');
            }
        } catch (Exception $e) {
            //throw $th;
            dd($e);
            return redirect()->back()->with('internal server error');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ScientificSession $scientific_session)
    {
        $scientific_session->update(['status' => 0]);

        return redirect()->back()->with('delete', 'Scientifc Session Deleted Successfully');
    }

    public function scheduleSession($society, $conference)
    {
        return view('backend.schedule-plan.scientific-session.schedule-session');
    }
}
