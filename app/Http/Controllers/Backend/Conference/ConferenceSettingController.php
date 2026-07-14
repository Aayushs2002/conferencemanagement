<?php

namespace App\Http\Controllers\Backend\Conference;

use App\Http\Controllers\Controller;
use App\Models\Conference\Conference;
use App\Models\Conference\ConferenceCustomCss;
use App\Models\Conference\ConferenceSetting;
use App\Services\File\FileService;
use Carbon\Carbon;
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
        // dd($request->all());
        try {
            $request->validate([
                'conference_id' => 'required|exists:conferences,id',
                'name' => 'required|string|max:255',
                'signature' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
                'payment_voucher_header_color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6})$/'],
                'registration_guideline' => 'nullable|file|mimes:pdf|max:5120',
                'registration_guideline_youtube' => 'nullable|url|max:500',
                'submission_guideline_youtube' => 'nullable|url|max:500',
                'expert_guideline_youtube' => 'nullable|url|max:500',
                'logo_display_type' => 'nullable',
                'payment_instruction' => 'nullable|string',
                'terms_conditions' => 'nullable|string',
                'privacy_policy' => 'nullable|string',
                'speaker_registration_required' => 'required|in:0,1',
                'registration_open_date' => 'nullable|date',
                'workshop_registration_open_date' => 'nullable|date',
                'workshop_application_deadline' => 'nullable|date',
                'show_stats_dashboard' => 'required|in:0,1',
                'conference_registration_verification_for_all' => 'required|in:0,1',
                'addon_availability' => 'required|in:both,participant_only,accompany_only',
                'gala_dinner_enabled' => 'required|in:0,1',
                'submission_cc_emails' => 'nullable|string',
                'reviewer_assignment_cc_emails' => 'nullable|string',
                'conference_registration_cc_emails' => 'nullable|string',
                'workshop_registration_cc_emails' => 'nullable|string',
                'closing_message' => 'nullable|string',
                'portal_access_end_at' => 'nullable|date',
                'cpd_points_required' => 'required|in:0,1',

            ]);

            $type = 'success';

            $conferenceSetting = ConferenceSetting::where('conference_id', $request->conference_id)->first();

            $signaturePath = $conferenceSetting->signature ?? null;
            $guidelinePath = $conferenceSetting->registration_guideline ?? null;

            if ($request->hasFile('signature')) {
                if (! empty($signaturePath)) {
                    $this->file_service->deleteFile($signaturePath, 'conference/voucher/signature/');
                }

                $signaturePath = $this->file_service->fileUpload(
                    $request->file('signature'),
                    'voucher_signature',
                    'conference/voucher/signature/'
                );
            }

            if ($request->hasFile('registration_guideline')) {
                if (! empty($guidelinePath)) {
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
                'payment_voucher_header_color' => $request->payment_voucher_header_color,
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
                'workshop_registration_open_date' => $request->workshop_registration_open_date,
                'workshop_application_deadline' => $request->workshop_application_deadline,
                'cpd_points_required' => $request->cpd_points_required,
                'show_stats_dashboard' => $request->show_stats_dashboard,
                'conference_registration_verification_for_all' => $request->conference_registration_verification_for_all,
                'addon_availability' => $request->addon_availability,
                'gala_dinner_enabled' => $request->gala_dinner_enabled,
                'submission_cc_emails' => $request->submission_cc_emails,
                'reviewer_assignment_cc_emails' => $request->reviewer_assignment_cc_emails,
                'conference_registration_cc_emails' => $request->conference_registration_cc_emails,
                'workshop_registration_cc_emails' => $request->workshop_registration_cc_emails,
                'closing_message' => $request->closing_message,
                'portal_access_end_at' => $request->filled('portal_access_end_at')
                    ? Carbon::parse($request->portal_access_end_at)
                    : null,
            ];

            if ($conferenceSetting) {
                $conferenceSetting->update($data);
                $message = 'Conference Setting updated successfully';
            } else {
                ConferenceSetting::create($data);
                $message = 'Conference Setting created successfully';
            }

            // Handle Custom CSS
            if ($request->has('custom_css')) {
                ConferenceCustomCss::updateOrCreate(
                    ['conference_id' => $request->conference_id],
                    [
                        'section_name' => 'global',
                        'custom_css' => $request->custom_css,
                        'status' => 1,
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
