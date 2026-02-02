<?php

namespace App\Http\Controllers\Backend\Workshop;

use App\Http\Controllers\Controller;
use App\Models\Workshop\WorkshopCertificate;
use App\Services\File\FileService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class WorkshopCertificateController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected FileService $file_service) {}

    /**
     * Display a listing of the resource.
     */
    public function index($society, $conference, $workshop)
    {
        $workshop_certificate = WorkshopCertificate::where(['workshop_id' => $workshop->id, 'status' => 1])->first();
        return view('backend.workshop.workshop-certificate.index', compact('society', 'conference', 'workshop', 'workshop_certificate'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($society, $conference, $workshop)
    {
        return view('backend.workshop.workshop-certificate.create', compact('society', 'conference', 'workshop'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $society, $conference, $workshop)
    {
        try {
            $rules = [
                'background_image' => 'required|mimes:jpg,png',
            ];
            $validated = $request->validate($rules);

            if (!empty($validated['background_image'])) {
                $validated['background_image'] = $this->file_service->fileUpload($validated['background_image'], 'certificate_background_image', 'workshop/certificate/background/');
            }
            
            $validated['workshop_id'] = $workshop->id;
            
            WorkshopCertificate::create($validated);
            return redirect()->route('workshop-certificate.index', [$society, $conference, $workshop])->with('status', 'Workshop Certificate Setting Added Successfully');
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
    public function edit($society, $conference, $workshop, WorkshopCertificate $workshop_certificate)
    {
        // $this->authorize('edit', $workshop_certificate);

        return view('backend.workshop.workshop-certificate.create', compact('society', 'conference', 'workshop', 'workshop_certificate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $society, $conference, $workshop, WorkshopCertificate $workshop_certificate)
    {
        try {
            $rules = [
                'background_image' => 'nullable|mimes:jpg,png',
            ];
            $validated = $request->validate($rules);
            
            if (!empty($validated['background_image'])) {
                $this->file_service->deleteFile($workshop_certificate->background_image, 'workshop/certificate/background/');

                $validated['background_image'] = $this->file_service->fileUpload($validated['background_image'], 'certificate_background_image', 'workshop/certificate/background/');
            }

            $workshop_certificate->update($validated);
            return redirect()->route('workshop-certificate.index', [$society, $conference, $workshop])->with('status', 'Certificate Setting Updated Successfully');
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
}
