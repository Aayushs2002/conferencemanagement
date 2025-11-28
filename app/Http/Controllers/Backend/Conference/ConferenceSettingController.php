<?php

namespace App\Http\Controllers\Backend\Conference;

use App\Http\Controllers\Controller;
use App\Models\Conference\Conference;
use App\Models\Conference\ConferenceSetting;
use App\Models\Conference\ConferenceCustomCss;
use App\Services\File\FileService;
use Exception;
use Illuminate\Http\Request;

class ConferenceSettingController extends Controller
{
    public function __construct(protected FileService $file_service) {}

    public function conferenceSetting(Request $request)
    {
        $conference = Conference::where('id', $request->id)->first();
        $conferenceSetting = ConferenceSetting::where('conference_id', $conference->id)->first();
        return view('backend.conference.conference-setting', compact('conference', 'conferenceSetting'));
    }

    public function conferenceSettingSubmit(Request $request)
    {
        try {
            $request->validate([
                'conference_id' => 'required|exists:conferences,id',
                'name' => 'required|string|max:255',
                'signature' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
                'registration_guideline' => 'nullable|file|mimes:pdf|max:5120',
                'registration_guideline_youtube' => 'nullable|url|max:500',
                'submission_guideline_youtube' => 'nullable|url|max:500',
                'expert_guideline_youtube' => 'nullable|url|max:500',
                'logo_display_type' => 'nullable',
                'payment_instruction' => 'nullable|string',
                'terms_conditions' => 'nullable|string',
                'privacy_policy' => 'nullable|string',
                'speaker_registration_required' => 'required|in:0,1',
                'registration_open_date' => 'nullable|date'
            ]);

            $type = 'success';

            $conferenceSetting = ConferenceSetting::where('conference_id', $request->conference_id)->first();

            $signaturePath = $conferenceSetting->signature ?? null;
            $guidelinePath = $conferenceSetting->registration_guideline ?? null;

            if ($request->hasFile('signature')) {
                if (!empty($signaturePath)) {
                    $this->file_service->deleteFile($signaturePath, 'conference/voucher/signature/');
                }

                $signaturePath = $this->file_service->fileUpload(
                    $request->file('signature'),
                    'voucher_signature',
                    'conference/voucher/signature/'
                );
            }

            if ($request->hasFile('registration_guideline')) {
                if (!empty($guidelinePath)) {
                    $this->file_service->deleteFile($guidelinePath, 'conference/registration-guideline/');
                }

                $guidelinePath = $this->file_service->fileUpload(
                    $request->file('registration_guideline'),
                    'registration_guideline',
                    'conference/registration-guideline/'
                );
            }

            $data = [
                'conference_id' => $request->conference_id,
                'name' => $request->name,
                'signature' => $signaturePath,
                'registration_guideline' => $guidelinePath,
                'registration_guideline_youtube' => $request->registration_guideline_youtube,
                'submission_guideline_youtube' => $request->submission_guideline_youtube,
                'expert_guideline_youtube' => $request->expert_guideline_youtube,
                'logo_display_type' => $request->logo_display_type,
                'payment_instruction' => $request->payment_instruction,
                'terms_conditions' => $request->terms_conditions,
                'privacy_policy' => $request->privacy_policy,
                'speaker_registration_required' => $request->speaker_registration_required,
                'registration_open_date' => $request->registration_open_date,
            ];

            if ($conferenceSetting) {
                $conferenceSetting->update($data);
                $message = "Conference Setting updated successfully";
            } else {
                ConferenceSetting::create($data);
                $message = "Conference Setting created successfully";
            }

            // Handle Custom CSS
            if ($request->has('custom_css')) {
                ConferenceCustomCss::updateOrCreate(
                    ['conference_id' => $request->conference_id],
                    [
                        'section_name' => 'global',
                        'custom_css' => $request->custom_css,
                        'status' => 1
                    ]
                );
            }
        } catch (\Exception $e) {
            // dd($e->getMessage());
            $type = 'error';
            $message = $e->getMessage();
        }

        return response()->json(['type' => $type, 'message' => $message]);
    }
}
