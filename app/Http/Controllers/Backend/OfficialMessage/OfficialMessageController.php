<?php

namespace App\Http\Controllers\Backend\OfficialMessage;

use App\Http\Controllers\Controller;
use App\Models\Conference\OfficialMessage;
use App\Services\File\FileService;
use Exception;
use Illuminate\Http\Request;

class OfficialMessageController extends Controller
{
    public function __construct(protected FileService $fileService) {}

    /**
     * Display a listing of the resource.
     */
    public function index($society, $conference)
    {
        $official_messages = OfficialMessage::where(['conference_id' => $conference->id, 'status' => 1])->get();
        return view('backend.official-message.index', compact('society', 'conference', 'official_messages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($society, $conference)
    {
        return view('backend.official-message.create', compact('society', 'conference'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $society, $conference)
    {
        try {
            $rules = [
                'full_name' => 'required',
                'designation' => 'required',
                'image' => 'nullable|mimes:jpg,png',
                'message' => 'required',
            ];

            $validated = $request->validate($rules);

            if (!empty($validated['image'])) {
                //file uplaod function parameter required file,name,location
                $validated['image'] = $this->fileService->fileUpload($validated['image'], 'official_message_image', 'offical-message/image');
            }


            $validated['conference_id'] = $conference->id;

            OfficialMessage::create($validated);

            return redirect()->route('official-message.index', [$society, $conference])->with('status', 'Official Message Added Successfully');
        } catch (Exception $e) {
            throw $e;
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
    public function edit($society, $conference, OfficialMessage $official_message)
    {
        // dd($official_message);
        return view('backend.official-message.create', compact('society', 'conference', 'official_message'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $society, $conference, OfficialMessage $official_message)
    {
        try {
            $rules = [
                'full_name' => 'required',
                'designation' => 'required',
                'image' => 'nullable|mimes:jpg,png',
                'message' => 'required',
            ];


            $validated = $request->validate($rules);

            if (!empty($validated['image'])) {
                $this->fileService->deleteFile($official_message->image, 'official_message_image/image');

                //file uplaod function parameter required file,name,location
                $validated['image'] = $this->fileService->fileUpload($validated['image'], 'official_message_image', 'offical-message/image');
            }



            $official_message->update($validated);

            return redirect()->route('official-message.index', [$society, $conference])->with('status', 'Offical Message Updated Successfully');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($society, $conference, OfficialMessage $official_message)
    {
        try {
            $official_message->update([
                'status' => 0
            ]);
            return redirect()->back()->with('status', 'Offical Message Deleted Successfuly');
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
