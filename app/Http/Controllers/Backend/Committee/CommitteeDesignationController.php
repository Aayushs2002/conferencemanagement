<?php

namespace App\Http\Controllers\Backend\Committee;

use App\Http\Controllers\Controller;
use App\Models\Committee\CommitteeDesignation;
use Exception;
use Illuminate\Http\Request;

class CommitteeDesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($society, $conference)
    {
        // $societyDetail = society_detail();

        // if (is_super_admin() && empty($societyDetail)) {
        //     return redirect()->route('dashboard');
        // }
        // if (is_society_admin()) {
        //     $committee_designations = CommitteeDesignation::where([
        //         'society_id' => current_user()->societies->value('id'),
        //         'status' => 1
        //     ])->latest()->get();
        // } elseif (is_super_admin()) {
        //     $committee_designations = CommitteeDesignation::where([
        //         'society_id' => $societyDetail->id,
        //         'status' => 1
        //     ])->latest()->get();
        // } else {
        //     return redirect()->route('dashboard');
        // }

        $committee_designations = CommitteeDesignation::where([
            'society_id' => $society->id,
            'status' => 1
        ])->latest()->get();

        // dd($committee_designations);
        return view('backend.committee.committee-designation.index', compact('committee_designations', 'society', 'conference'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($society, $conference)
    {
        return view('backend.committee.committee-designation.create', compact('society', 'conference'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $society, $conference)
    {
        try {
            $validated = $request->validate([
                'designation' => 'required'
            ]);

            $validated['society_id'] = $society->id;

            CommitteeDesignation::create($validated);

            return redirect()->route('committe-designation.index', [$society, $conference])->with('status', 'Designation Added Successfully');
        } catch (Exception $e) {
            throw $e;
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
    public function edit($society, $conference, CommitteeDesignation $committe_designation)
    {
        return view('backend.committee.committee-designation.create', compact('committe_designation', 'society', 'conference'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $society, $conference, CommitteeDesignation $committe_designation)
    {
        try {
            $validated = $request->validate([
                'designation' => 'required'
            ]);

            $committe_designation->update($validated);

            return redirect()->route('committe-designation.index', [$society, $conference])->with('status', 'Designation Updated Successfully');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($society, $conference, CommitteeDesignation $committe_designation)
    {
        if ($committe_designation->committeeMembers->isNotEmpty()) {
            $message = 'Cannot delete this designation.';
        } else {
            $committe_designation->update(['status' => 0]);
            $message = 'Designation Deleted Successfully.';
        }
        return redirect()->route('committe-designation.index', [$society, $conference])->with('delete', $message);
    }
}
