<?php

namespace App\Http\Controllers\Backend\Participant;

use App\Http\Controllers\Controller;
use App\Models\User\Society;
use App\Models\Workshop\Workshop;
use App\Models\Workshop\WorkshopRegistration;
use Exception;
use Illuminate\Http\Request;

class WorkshopTrainerController extends Controller
{
    /**
     * Display a listing of trainers for participant's approved workshop
     */
    public function index($society, $conference, $workshop)
    {
        // Verify ownership and approval status
        if ($workshop->created_by != current_user()->id || $workshop->approval_status != 'approved') {
            abort(403, 'Unauthorized access to this workshop.');
        }

        $trainers = WorkshopRegistration::where([
            'workshop_id' => $workshop->id,
            'registrant_type' => 2,
            'status' => 1
        ])->latest()->get();

        return view('backend.participant.workshop-trainer.index', compact('workshop', 'trainers', 'society', 'conference'));
    }

    /**
     * Show the form for creating a new trainer
     */
    public function create($society, $conference, $workshop)
    {
        // Verify ownership and approval status
        if ($workshop->created_by != current_user()->id || $workshop->approval_status != 'approved') {
            abort(403, 'Unauthorized access to this workshop.');
        }

        $society = Society::with(['users' => function ($query) {
            $query->where('type', 3)->orderByDesc('id');
        }])->where([
            'id' => $conference->society_id,
            'status' => 1
        ])->first();

        $users = $society ? $society->users : collect();
        return view('backend.participant.workshop-trainer.create', compact('workshop', 'society', 'conference', 'users'));
    }

    /**
     * Store a newly created trainer
     */
    public function store(Request $request, $society, $conference)
    {
        try {
            $rules = [
                'workshop_id' => 'required',
                'user_id' => 'required',
            ];

            $validated = $request->validate($rules);
            $workshop = Workshop::where('id', $validated['workshop_id'])->first();

            // Verify ownership and approval status
            if (!$workshop || $workshop->created_by != current_user()->id || $workshop->approval_status != 'approved') {
                return redirect()->back()->with('error', 'Unauthorized access to this workshop.');
            }

            $validated['registrant_type'] = 2;
            $validated['token'] = random_word(60);
            $validated['verified_status'] = 1;

            WorkshopRegistration::create($validated);

            return redirect()->route('my-society.conference.my-workshop.trainer.index', [$society, $conference, $workshop])
                ->with('status', 'Trainer Added Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing a trainer
     */
    public function edit($society, $conference, $workshop, WorkshopRegistration $trainer)
    {
        // Verify ownership and approval status
        if ($workshop->created_by != current_user()->id || $workshop->approval_status != 'approved') {
            abort(403, 'Unauthorized access to this workshop.');
        }

        $society = Society::with(['users' => function ($query) {
            $query->where('type', 3)->orderByDesc('id');
        }])->where([
            'id' => $conference->society_id,
            'status' => 1
        ])->first();

        $users = $society ? $society->users : collect();
        return view('backend.participant.workshop-trainer.create', compact('workshop', 'trainer', 'society', 'conference', 'users'));
    }

    /**
     * Update the specified trainer
     */
    public function update(Request $request, $society, $conference, WorkshopRegistration $trainer)
    {
        try {
            $rules = [
                'workshop_id' => 'required',
                'user_id' => 'required',
            ];

            $validated = $request->validate($rules);
            $workshop = Workshop::where('id', $validated['workshop_id'])->first();

            // Verify ownership and approval status
            if (!$workshop || $workshop->created_by != current_user()->id || $workshop->approval_status != 'approved') {
                return redirect()->back()->with('error', 'Unauthorized access to this workshop.');
            }

            $validated['registrant_type'] = 2;
            $trainer->update($validated);

            return redirect()->route('my-society.conference.my-workshop.trainer.index', [$society, $conference, $workshop])
                ->with('status', 'Trainer Updated Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified trainer
     */
    public function destroy($society, $conference, $workshop, WorkshopRegistration $trainer)
    {
        // Verify ownership and approval status
        if ($workshop->created_by != current_user()->id || $workshop->approval_status != 'approved') {
            return redirect()->back()->with('error', 'Unauthorized access to this workshop.');
        }

        $trainer->update(['status' => 0]);
        return redirect()->back()->with('delete', 'Trainer Deleted Successfully');
    }
}
