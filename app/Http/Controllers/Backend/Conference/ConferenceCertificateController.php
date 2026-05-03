<?php

namespace App\Http\Controllers\Backend\Conference;

use App\Http\Controllers\Controller;
use App\Models\Conference\ConferenceCertificate;
use App\Services\File\FileService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ConferenceCertificateController extends Controller
{
    use AuthorizesRequests;

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
                'include_title' => 'required|in:0,1',
                'include_signature' => 'required|in:0,1',
                'show_presentation_type' => 'required|in:0,1',
                'signatures' => 'nullable|array',
                'signatures.*' => 'mimes:jpg,png',
                'custom_css' => 'nullable|string',
                'name' => 'nullable|array',
                'name.*' => 'required_with:signatures.*',
                'designation' => 'nullable|array',
                'designation.*' => 'required_with:signatures.*',
                'signature_order' => 'nullable|array',
                'signature_order.*' => 'nullable|integer|min:1',
            ];
            $validated = $request->validate($rules);
            if ((int) $validated['include_signature'] === 1 && empty($validated['signatures'])) {
                return redirect()->back()->withInput()->withErrors([
                    'signatures' => 'Please add at least one signature image or disable signature inclusion.',
                ]);
            }

            if (!empty($validated['signatures']) && count($validated['signatures']) > 5) {
                return redirect()->back()->withInput()->with('delete', 'Images Limitation Crossed.');
            }

            if (!empty($validated['signatures'])) {
                $orders = [];
                foreach ($validated['signatures'] as $key => $pic) {
                    $orders[] = (int) ($validated['signature_order'][$key] ?? ($key + 1));
                }

                if (count($orders) !== count(array_unique($orders))) {
                    return redirect()->back()->withInput()->withErrors([
                        'signature_order' => 'Signature display order must be unique for all signatures.',
                    ]);
                }
            }

            if (!empty($validated['background_image'])) {
                $validated['background_image'] = $this->file_service->fileUpload($validated['background_image'], 'certificate_background_image', 'conference/conference/certificate/background/');
            }
            if (!empty($validated['signatures'])) {
                foreach ($validated['signatures'] as $key => $pic) {
                    $validated['signature'][] = [
                        'fileName' => $this->file_service->fileUpload($pic, 'certificate-signature', 'conference/conference/certificate/signature/'),
                        'name' => $validated['name'][$key] ?? '',
                        'designation' => $validated['designation'][$key] ?? '',
                        'order' => (int) ($validated['signature_order'][$key] ?? ($key + 1)),
                    ];
                }

                usort($validated['signature'], function ($a, $b) {
                    return ((int) ($a['order'] ?? 9999)) <=> ((int) ($b['order'] ?? 9999));
                });

                $validated['signature'] = array_values($validated['signature']);
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
        $this->authorize('edit', $conference_certificate);

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
                'include_title' => 'required|in:0,1',
                'include_signature' => 'required|in:0,1',
                'show_presentation_type' => 'required|in:0,1',
                'signatures' => 'nullable|array',
                'signatures.*' => 'mimes:jpg,png',
                'custom_css' => 'nullable|string',
                'name' => 'nullable|array',
                'name.*' => 'required_with:signatures.*',
                'designation' => 'nullable|array',
                'designation.*' => 'required_with:signatures.*',
                'signature_order' => 'nullable|array',
                'signature_order.*' => 'nullable|integer|min:1',
                'name_old' => 'nullable|array',
                'name_old.*' => 'nullable',
                'designation_old' => 'nullable|array',
                'designation_old.*' => 'nullable',
                'signature_order_old' => 'nullable|array',
                'signature_order_old.*' => 'nullable|integer|min:1',
            ];
            $validated = $request->validate($rules);
            $countOldImages = 0;
            if (!empty($conference_certificate->signature)) {
                $countOldImages = count($conference_certificate->signature);
            }
            if (!empty($validated['signatures']) && count($validated['signatures']) + $countOldImages > 5) {
                return redirect()->back()->withInput()->with('delete', 'Images Limitation Crossed.');
            }

            if ((int) $validated['include_signature'] === 1 && $countOldImages === 0 && empty($validated['signatures'])) {
                return redirect()->back()->withInput()->withErrors([
                    'signatures' => 'Please add at least one signature image or disable signature inclusion.',
                ]);
            }

            $allOrders = [];
            if (!empty($conference_certificate->signature)) {
                foreach ($conference_certificate->signature as $key => $signature) {
                    $allOrders[] = (int) ($validated['signature_order_old'][$key] ?? ($signature['order'] ?? ($key + 1)));
                }
            }

            if (!empty($validated['signatures'])) {
                foreach ($validated['signatures'] as $key => $pic) {
                    $allOrders[] = (int) ($validated['signature_order'][$key] ?? ($key + 1));
                }
            }

            if (!empty($allOrders) && count($allOrders) !== count(array_unique($allOrders))) {
                return redirect()->back()->withInput()->withErrors([
                    'signature_order' => 'Signature display order must be unique for all signatures.',
                    'signature_order_old' => 'Signature display order must be unique for all signatures.',
                ]);
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
                    $oldImages[$k]['name'] = $validated['name_old'][$k] ?? ($oldImages[$k]['name'] ?? '');
                    $oldImages[$k]['designation'] = $validated['designation_old'][$k] ?? ($oldImages[$k]['designation'] ?? '');
                    $oldImages[$k]['order'] = (int) ($validated['signature_order_old'][$k] ?? ($oldImages[$k]['order'] ?? ($k + 1)));
                }
            }
            if (!empty($validated['signatures'])) {
                foreach ($validated['signatures'] as $key => $pic) {
                    $newImage  = [
                        'fileName' => $this->file_service->fileUpload($pic, 'certificate-signature', 'conference/conference/certificate/signature/'),
                        'name' => $validated['name'][$key] ?? '',
                        'designation' => $validated['designation'][$key] ?? '',
                        'order' => (int) ($validated['signature_order'][$key] ?? ($key + 1)),
                    ];

                    $newImages[] = $newImage;
                }
            }

            $validated['signature'] = array_merge($oldImages, $newImages);

            usort($validated['signature'], function ($a, $b) {
                return ((int) ($a['order'] ?? 9999)) <=> ((int) ($b['order'] ?? 9999));
            });

            $validated['signature'] = array_values($validated['signature']);
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

    public function deleteImage(ConferenceCertificate $conferenceCertificate, $signature)
    {
        $this->file_service->deleteFile($signature, 'conference/conference/certificate/signature/');

        $images = $conferenceCertificate->signature;

        if (count($conferenceCertificate->signature) == 1) {
            $conferenceCertificate->update(['signature' => null]);
        } else {
            foreach ($images as $key => $value) {
                if ($value['fileName'] == $signature) {
                    unset($images[$key]);
                    break;
                }
            }
            $images = array_values($images);
            $conferenceCertificate->update(['signature' => $images]);
        }

        return redirect()->back()->with('delete', 'Image Deleted Successfully');
    }
}
