<?php

namespace App\Http\Controllers\Backend\Conference;

use App\Http\Controllers\Controller;
use App\Models\Conference\ConferenceCertificate;
use App\Services\File\FileService;
use Exception;
use Illuminate\Http\Request;

class ConferenceCertificateController extends Controller
{
    public function __construct(protected FileService $file_service) {}

    /**
     * Display a listing of the resource.
     */
    public function index($society, $conference)
    {
        $conference_certificate = ConferenceCertificate::where(['conference_id' => $conference->id, 'status' => 1])->first();
        return view('backend.conference.conference-certificate.index', compact('society', 'conference', 'conference_certificate'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($society, $conference)
    {
        return view('backend.conference.conference-certificate.create', compact('society', 'conference'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $society, $conference)
    {
        try {
            $rules = [
                'background_image' => 'required|mimes:jpg,png',
                'signatures' => 'required',
                'signatures.*' => 'mimes:jpg,png',
                'name' => 'required',
                'name.*' => 'required',
                'designation' => 'required',
                'designation.*' => 'required',
            ];
            $validated = $request->validate($rules);
            if (!empty($validated['signatures']) && count($validated['signatures']) > 5) {
                return redirect()->back()->withInput()->with('delete', 'Images Limitation Crossed.');
            }

            if (!empty($validated['background_image'])) {
                $validated['background_image'] = $this->file_service->fileUpload($validated['background_image'], 'certificate_background_image', 'conference/conference/certificate/background/');
            }
            if (!empty($validated['signatures'])) {
                foreach ($validated['signatures'] as $key => $pic) {
                    $validated['signature'][] = [
                        'fileName' => $this->file_service->fileUpload($pic, 'certificate-signature', 'conference/conference/certificate/signature/'),
                        'name' => $validated['name'][$key],
                        'designation' => $validated['designation'][$key]
                    ];
                }
            }
            $validated['conference_id'] = $conference->id;
            // dd($validated);
            ConferenceCertificate::create($validated);
            return redirect()->route('conference-certificate.index', [$society, $conference])->with('status', 'Conference Setting Added Successfully');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($society, $conference, ConferenceCertificate $conference_certificate)
    {
        return view('backend.conference.conference-certificate.create', compact('society', 'conference', 'conference_certificate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $society, $conference, ConferenceCertificate $conference_certificate)
    {
        try {
            $rules = [
                'background_image' => 'nullable|mimes:jpg,png',
                'signatures' => 'nullable',
                'signatures.*' => 'mimes:jpg,png',
                'name' => 'nullable',
                'name.*' => 'nullable',
                'designation' => 'nullable',
                'designation.*' => 'nullable',
                'name_old' => 'nullable',
                'name_old.*' => 'nullable',
                'designation_old' => 'nullable',
                'designation_old.*' => 'nullable',
            ];
            $validated = $request->validate($rules);
            $countOldImages = 0;
            if (!empty($conference_certificate->signature)) {
                $countOldImages = count($conference_certificate->signature);
            }
            if (!empty($validated['signatures']) && count($validated['signatures']) + $countOldImages > 5) {
                return redirect()->back()->withInput()->with('delete', 'Images Limitation Crossed.');
            }
            if (!empty($validated['background_image'])) {
                $this->file_service->deleteFile($conference_certificate->background_image, 'conference/conference/certificate/background/');

                $validated['background_image'] = $this->file_service->fileUpload($validated['background_image'], 'certificate_background_image', 'conference/conference/certificate/background/');
            }

            $newImages = [];
            $oldImages = [];
            if (!empty($conference_certificate->signature)) {
                $oldImages = $conference_certificate->signature;
                foreach ($oldImages as $k => $v) {
                    $oldImages[$k]['name'] = $validated['name_old'][$k];
                    $oldImages[$k]['designation'] = $validated['designation_old'][$k];
                }
            }
            if (!empty($validated['signatures'])) {
                foreach ($validated['signatures'] as $key => $pic) {
                    $newImage  = [
                        'fileName' => $this->file_service->fileUpload($pic, 'certificate-signature', 'conference/conference/certificate/signature/'),
                        'name' => $validated['name'][$key],
                        'designation' => $validated['designation'][$key]
                    ];

                    $newImages[] = $newImage;
                }
            }

            $validated['signature'] = array_merge($oldImages, $newImages);
            // dd($validated);
            $conference_certificate->update($validated);
            return redirect()->route('conference-certificate.index', [$society, $conference])->with('status', 'Certificate Setting Updated Successfully');
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function deleteImage(ConferenceCertificate $conference_certificate, $signature)
    {
        dd('da');
        $this->file_service->deleteFile($signature, 'hotel/images');

        $images = $conference_certificate->signature;

        if (count($conference_certificate->signature) == 1) {
            $conference_certificate->update(['signature' => null]);
        } else {
            foreach ($images as $key => $value) {
                if ($value['fileName'] == $signature) {
                    unset($images[$key]);
                    break;
                }
            }
            $images = array_values($images);
            $conference_certificate->update(['signature' => $images]);
        }

        return redirect()->back()->with('delete', 'Image Deleted Successfully');
    }
}
