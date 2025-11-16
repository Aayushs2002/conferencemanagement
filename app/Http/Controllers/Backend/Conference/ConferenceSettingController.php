<?php

namespace App\Http\Controllers\Backend\Conference;

use App\Http\Controllers\Controller;
use App\Models\Conference\Conference;
use App\Models\Conference\ConferenceSetting;
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
            ]);

            $type = 'success';

            $conferenceSetting = ConferenceSetting::where('conference_id', $request->conference_id)->first();

            $signaturePath = $conferenceSetting->signature ?? null;
            $guidelinePath = $conferenceSetting->registration_guideline ?? null;

            if ($request->hasFile('signature')) {
                if (!empty($signaturePath)) {
                    $this->file_service->deleteFile($signaturePath, 'conference/voucher/signature/');
                }

                $signaturePath = $this->file_service->fileUpload($request->file('signature'),'voucher_signature','conference/voucher/signature/'
                );
            }

            if ($request->hasFile('registration_guideline')) {
                if (!empty($guidelinePath)) {
                    $this->file_service->deleteFile($guidelinePath, 'conference/registration-guideline/');
                }

                $guidelinePath = $this->file_service->fileUpload($request->file('registration_guideline'),'registration_guideline','conference/registration-guideline/'
                );
            }

            $data = [
                'conference_id' => $request->conference_id,
                'name' => $request->name,
                'signature' => $signaturePath,
                'registration_guideline' => $guidelinePath,
            ];

            if ($conferenceSetting) {
                $conferenceSetting->update($data);
                $message = "Conference Setting updated successfully";
            } else {
                ConferenceSetting::create($data);
                $message = "Conference Setting created successfully";
            }
        } catch (\Exception $e) {
            $type = 'error';
            $message = $e->getMessage();
        }

        return response()->json(['type' => $type, 'message' => $message]);
    }
}
