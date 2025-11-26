<?php

namespace App\Http\Controllers\Backend\Submission;

use App\Http\Controllers\Controller;
use App\Models\Conference\Conference;
use App\Models\SubmissionSetting;
use App\Services\File\FileService;
use Exception;
use Illuminate\Http\Request; 

class SubmissionSettingController extends Controller
{
    public function __construct(protected FileService $file_service) {}

    public function index(Request $request, $society, $conference)
    { 
        $conference = Conference::where(['id' => $conference->id, 'status' => 1])->first();
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
                'attachment_name' => 'nullable',
                'attachment_required' => 'nullable|boolean',
                'signature' => 'nullable|mimes:png,jpg',
                'scoring_allowed' => 'nullable|boolean',
                'contribution_enabled' => 'nullable|boolean',

            ]);

            $message = empty($validated['id']) ? 'Successfully inserted submission setting.' : 'Successfully updated submission setting';

            if (empty($validated['id'])) {
                if (!empty($validated['signature'])) {
                    //file uplaod function parameter required file,name,location
                    $validated['signature'] = $this->file_service->fileUpload($validated['signature'], 'scientific-session-signature', 'submission/setting/signature');
                }
                $submitData = SubmissionSetting::create($validated);
            } else {
                $submissionSetting = SubmissionSetting::whereId($validated['id'])->first();
                if (!empty($validated['signature'])) {
                    $this->file_service->deleteFile($submissionSetting->signature, 'conference/conference/logo');
                    //file uplaod function parameter required file,name,location
                    $validated['signature'] = $this->file_service->fileUpload($validated['signature'], 'scientific-session-signature', 'submission/setting/signature');
                }
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
