<?php

namespace App\Http\Controllers\Backend\Submission;

use App\Http\Controllers\Controller;
use App\Models\Conference\Conference;
use App\Models\SubmissionSetting;
use Exception;
use Illuminate\Http\Request;

class SubmissionSettingController extends Controller
{
    public function index(Request $request, $society, $conference)
    {
        // $conferenceDetail = conference_detail();

        // if (empty($conferenceDetail)) {
        //     return redirect()->route('dashboard');
        // }

        $conference = Conference::where(['id' => $conference->id, 'status' => 1])->first();
        // dd($conference);
        return view('backend.submission.submission-setting.index', compact('conference', 'society'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'conference_id' => 'required',
                'id' => 'nullable',
                'deadline' => 'nullable|date',
                'abstract_word_limit' => 'nullable|numeric',
                'key_word_limit' => 'nullable|numeric',
                'authors_limit' => 'nullable|numeric',
                'abstract_guidelines' => 'nullable',
                'oral_guidelines' => 'nullable',
                'poster_guidelines' => 'nullable',
                'oral_reviewer_guide' => 'nullable',
                'poster_reviewer_guide' => 'nullable',
            ]);

            $message = empty($validated['id']) ? 'Successfully inserted submission setting.' : 'Successfully updated submission setting';

            if (empty($validated['id'])) {
                $submitData = SubmissionSetting::create($validated);
            } else {
                $submissionSetting = SubmissionSetting::whereId($validated['id'])->first();
                $submitData = $submissionSetting->update($validated);
            }

            if (!$submitData) {
                throw new Exception("Error Processing Request", 1);
            }
            return redirect()->back()->with('status', $message);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
