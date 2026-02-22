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
                'background_image' => 'nullable|mimes:jpg,png',
                'signature_image' => 'nullable|mimes:jpg,png',
                'signature_name' => 'nullable|string|max:255',
            ];
            $validated = $request->validate($rules);

            if (!empty($validated['background_image'])) {
                $validated['background_image'] = $this->file_service->fileUpload($validated['background_image'], 'certificate_background_image', 'workshop/certificate/background/');
            }
            
            if (!empty($validated['signature_image'])) {
                $validated['signature_image'] = $this->file_service->fileUpload($validated['signature_image'], 'certificate_signature_image', 'workshop/certificate/signature/');
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
                'signature_image' => 'nullable|mimes:jpg,png',
                'signature_name' => 'nullable|string|max:255',
                'signature_designation' => 'nullable|string|max:255',
            ];
            $validated = $request->validate($rules);
            
            if (!empty($validated['background_image'])) {
                $this->file_service->deleteFile($workshop_certificate->background_image, 'workshop/certificate/background/');

                $validated['background_image'] = $this->file_service->fileUpload($validated['background_image'], 'certificate_background_image', 'workshop/certificate/background/');
            }
            
            if (!empty($validated['signature_image'])) {
                $this->file_service->deleteFile($workshop_certificate->signature_image, 'workshop/certificate/signature/');

                $validated['signature_image'] = $this->file_service->fileUpload($validated['signature_image'], 'certificate_signature_image', 'workshop/certificate/signature/');
            }

            $workshop_certificate->update($validated);
            return redirect()->route('workshop-certificate.index', [$society, $conference, $workshop])->with('status', 'Certificate Setting Updated Successfully');
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Generate certificate for workshop registration
     */
    public function generateCertificate($society, $conference, $workshop, $workshopRegistrationId)
    {
        $workshopRegistration = \App\Models\Workshop\WorkshopRegistration::findOrFail($workshopRegistrationId);
        
        // Load relationships
        $workshop->load('workshopCertificate', 'WorkshopVenueDetail', 'conference.conferenceCertificate', 'conference.conferenceSetting');
        $workshopRegistration->load('user.userDetail.namePrefix');
        
        // Get registrant name with prefix
        $user = $workshopRegistration->user;
        $registrantName = '';
        if ($user && $user->userDetail) {
            $prefix = $user->userDetail->namePrefix->prefix ?? '';
            $registrantName = trim($prefix . ' ' . $user->fullName($user));
        }
        
        // Get registrant type text (1 = Participant, 2 = Trainer)
        $registrantType = '';
        switch ($workshopRegistration->registrant_type) {
            case 1:
                $registrantType = 'Attendee';
                break;
            case 2:
                $registrantType = 'Faculty';
                break;
            default:
                $registrantType = 'Attendee';
                break;
        }
        
        return view('backend.workshop.generate-certificate', compact(
            'workshop',
            'conference',
            'workshopRegistration',
            'registrantName',
            'registrantType'
        ));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
