<?php

namespace App\Http\Controllers\Backend\ScientificSession;

use App\Http\Controllers\Controller;
use App\Models\Conference\Hall;
use Illuminate\Http\Request;

class HallController extends Controller
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

        $halls = Hall::where(['conference_id' => $conference->id, 'status' => 1])->get();
        return view('backend.schedule-plan.hall.index', compact('halls', 'society', 'conference'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($society, $conference)
    {
        // $conferenceDetail = conference_detail();

        // if (empty($conferenceDetail)) {
        //     return redirect()->route('dashboard');
        // }

        return view('backend.schedule-plan.hall.create', compact('society', 'conference'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $society, $conference)
    {
        try {
            $validated = $request->validate([
                'hall_name' => 'required'
            ]);
            $validated['conference_id'] = $conference->id;
            Hall::create($validated);
            return redirect()->route('hall.index', [$society, $conference])->with('status', 'Hall Created Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
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
    public function edit($society, $conference, Hall $hall)
    {
        return view('backend.schedule-plan.hall.create', compact('hall', 'society', 'conference'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $society, $conference, Hall $hall)
    {
        try {
            $validated = $request->validate([
                'hall_name' => 'required'
            ]);
            $hall->update($validated);
            return redirect()->route('hall.index', [$society, $conference])->with('status', 'Hall Updated Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($society, $conference, Hall $hall)
    {
        try {
            $hall->update(['status' => 0]);
            return redirect()->route('hall.index', [$society, $conference])->with('status', 'Hall Deleted Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }
}
