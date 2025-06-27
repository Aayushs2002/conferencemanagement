<?php

namespace App\Http\Controllers\Backend\Submission;

use App\Http\Controllers\Controller;
use App\Models\Conference\SubmissionCategoryMajorTrack;
use Illuminate\Http\Request;

class SubmissionCategoryMajorTrackContoller extends Controller
{
    public function index($society, $conference)
    {
        // $conferenceDetail = conference_detail();

        // if (empty($conferenceDetail)) {
        //     return redirect()->route('dashboard');
        // }

        $submissionCategoryMajortracks = SubmissionCategoryMajorTrack::where(['conference_id' => $conference->id, 'status' => 1])->get();
        return view('backend.submission.submission-category-majortrack.index', compact('submissionCategoryMajortracks', 'society', 'conference'));
    }


    public function create($society, $conference)
    {
        return view('backend.submission.submission-category-majortrack.create', compact('society', 'conference'));
    }

    public function store(Request $request, $society, $conference)
    {
        try {
            $conferenceDetail = conference_detail();
            $validated = $request->validate([
                'title' => 'required',
                'major_areas' => 'required'
            ]);
            $validated['conference_id'] = $conference->id;
            SubmissionCategoryMajorTrack::create($validated);
            return redirect()->route('submission.category-majortrack.index', $society, $conference)->with('status', 'Submission Category/Major Track Created Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    public function edit($society, $conference, SubmissionCategoryMajorTrack $submissionCategoryMajortrack)
    {
        return view('backend.submission.submission-category-majortrack.create', compact('submissionCategoryMajortrack', 'society', 'conference'));
    }

    public function update($society, $conference, SubmissionCategoryMajorTrack $submissionCategoryMajortrack, Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required',
                'major_areas' => 'required'
            ]);

            $submissionCategoryMajortrack->update($validated);
            return redirect()->route('submission.category-majortrack.index', [$society, $conference])->with('status', 'Submission Category/Major Track Updated Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    public function destroy($society, $conference, SubmissionCategoryMajorTrack $submissionCategoryMajortrack)
    {
        try {
            $submissionCategoryMajortrack->update(['status' => 0]);
            return redirect()->route('submission.category-majortrack.index', [$society, $conference])->with('status', 'Submission Category/Major Track Deleted Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }
}
