<?php

namespace App\Http\Controllers\Backend\Workshop\PassSetting;

use App\Http\Controllers\Controller;
use App\Models\Workshop\WorkshopPassSetting;
use App\Services\File\FileService;
use Exception;
use Illuminate\Http\Request;

class WorkshopPassSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(protected FileService $file_service) {}

    public function index($society, $conference)
    {
        $workshop_pass_setting = WorkshopPassSetting::where(['conference_id' => $conference->id, 'status' => 1])->first();

        return view('backend.workshop.pass-setting.index', compact('workshop_pass_setting', 'society', 'conference'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($society, $conference)
    {

        return view('backend.workshop.pass-setting.create', compact('society', 'conference'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $society, $conference)
    {
        try {
            $validated = $request->validate([
                'image' => 'required|mimes:png,jpg,',
            ]);
            $validated['conference_id'] = $conference->id;

            if (!empty($validated['image'])) {
                //file uplaod function parameter required file,name,location
                $validated['image'] = $this->file_service->fileUpload($validated['image'], 'pass_image', 'workshop/pass');
            }
            WorkshopPassSetting::create($validated);

            return redirect()->route('workshop-pass-settings.index', [$society, $conference])->with('status', 'Pass Setting Added Successfully');
        } catch (Exception $e) {
            dd($e);
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($society, $conference, WorkshopPassSetting $workshop_pass_setting)
    {
        return view('backend.workshop.pass-setting.create', compact(
            'society',
            'conference',
            'workshop_pass_setting',
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $society, $conference, WorkshopPassSetting $workshop_pass_setting)
    {
        try {
            $validated = $request->validate([
                'image' => 'nullable|mimes:png,jpg,',

            ]);
            if (!empty($validated['image'])) {
                //deleting the file deleteFile function parameter required file,location
                $this->file_service->deleteFile($workshop_pass_setting->image, 'workshop/pass');
                //file uplaod function parameter required file,name,location
                $validated['image'] = $this->file_service->fileUpload($validated['image'], 'pass_image', 'workshop/pass');
            }
            $workshop_pass_setting->update($validated);

            return redirect()->route('workshop-pass-settings.index', [$society, $conference])->with('status', 'Pass Setting Updated Successfully');
        } catch (Exception $e) {
            dd($e);
            return redirect()->back()->with('delete', 'Internal Server Error');
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
