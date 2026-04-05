<?php

namespace App\Http\Controllers\Backend\Submission;

use App\Http\Controllers\Controller;
use App\Models\Conference\SubmissionCategoryMajorTrack;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SubmissionCategoryMajorTrackContoller extends Controller
{
    use AuthorizesRequests;

    private function getAssignableUsers($conference)
    {
        return User::whereHas('conferencePermissions', function ($query) use ($conference) {
            $query->where('conference_user_permission.conference_id', $conference->id)
                ->where('name', 'View Submission');
        })
            ->whereNotIn('type', [1, 2])
            ->with('userDetail')
            ->orderBy('f_name')
            ->orderBy('l_name')
            ->get();
    }

    public function index($society, $conference)
    {
        $submissionCategoryMajortracks = SubmissionCategoryMajorTrack::with('managers')
            ->where(['conference_id' => $conference->id, 'status' => 1])
            ->get();
        return view('backend.submission.submission-category-majortrack.index', compact('submissionCategoryMajortracks', 'society', 'conference'));
    }


    public function create($society, $conference)
    {
        $assignableUsers = $this->getAssignableUsers($conference);

        return view('backend.submission.submission-category-majortrack.create', compact('society', 'conference', 'assignableUsers'));
    }

    public function store(Request $request, $society, $conference)
    {
        try {
            $conferenceDetail = conference_detail();
            $validated = $request->validate([
                'title' => 'required',
                'major_areas' => 'required',
                'manager_user_ids' => 'nullable|array',
                'manager_user_ids.*' => 'nullable|exists:users,id',
            ]);
            $validated['conference_id'] = $conference->id;
            $submissionCategoryMajorTrack = SubmissionCategoryMajorTrack::create($validated);

            $submissionCategoryMajorTrack->managers()->sync($request->input('manager_user_ids', []));
            return redirect()->route('submission.category-majortrack.index', [$society, $conference])->with('status', 'Submission Category/Major Track Created Successfully');
        } catch (\Exception $e) {
            // dd($e);
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    public function edit($society, $conference, SubmissionCategoryMajorTrack $submissionCategoryMajortrack)
    {
        // dd('ad');
        $this->authorize('edit', $submissionCategoryMajortrack);

        $assignableUsers = $this->getAssignableUsers($conference);

        return view('backend.submission.submission-category-majortrack.create', compact('submissionCategoryMajortrack', 'society', 'conference', 'assignableUsers'));
    }

    public function update($society, $conference, SubmissionCategoryMajorTrack $submissionCategoryMajortrack, Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required',
                'major_areas' => 'required',
                'manager_user_ids' => 'nullable|array',
                'manager_user_ids.*' => 'nullable|exists:users,id',
            ]);

            $submissionCategoryMajortrack->update($validated);
            $submissionCategoryMajortrack->managers()->sync($request->input('manager_user_ids', []));
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
