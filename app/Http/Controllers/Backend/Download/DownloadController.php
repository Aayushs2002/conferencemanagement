<?php

namespace App\Http\Controllers\Backend\Download;

use App\Http\Controllers\Controller;
use App\Models\Download\Download;
use App\Services\File\FileService;
use Exception;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(protected FileService $fileService) {}

    public function index($society, $conference)
    {
        // $conferenceDetail = conference_detail();

        // if (empty($conferenceDetail)) {
        //     return redirect()->route('dashboard');
        // }
        $downloads = Download::where(['conference_id' => $conference->id, 'status' => 1])->get();
        return view('backend.download.index', compact('downloads', 'society', 'conference'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($society, $conference)
    {
        return view('backend.download.create', compact('society', 'conference'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $society, $conference)
    {
        try {
            $rules = [
                'title' => 'required|unique:downloads,title',
                'date' => 'required|date',
                'file' => 'required|mimes:jpg,png,pdf,doc,docs,xlsx|max:5120',
                'image' => 'nullable|mimes:jpg,png',
                'description' => 'nullable'
            ];

            $validated = $request->validate($rules);
            if (!empty($validated['file'])) {
                //file uplaod function parameter required file,name,location
                $validated['file'] = $this->fileService->fileUpload($validated['file'], 'downloads_file', 'download/file');
            }
            if (!empty($validated['image'])) {
                //file uplaod function parameter required file,name,location
                $validated['image'] = $this->fileService->fileUpload($validated['image'], 'downloads_cover_image', 'download/image');
            }


            $validated['conference_id'] = $conference->id;

            Download::create($validated);

            return redirect()->route('download.index', [$society, $conference])->with('status', 'File Added Successfully');
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
    public function edit($society, $conference, Download $download)
    {
        return view('backend.download.create', compact('download', 'society', 'conference'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $society, $conference, Download $download)
    {
        try {
            $rules = [
                'title' => 'required|unique:downloads,title,' . $download->id,
                'date' => 'required|date',
                'file' => 'nullable|mimes:jpg,png,pdf,doc,docs,xlsx|max:5120',
                'image' => 'nullable|mimes:jpg,png',
                'description' => 'nullable'
            ];

            $validated = $request->validate($rules);
            if (!empty($validated['file'])) {
                $this->fileService->deleteFile($download->file, 'download/file');

                //file uplaod function parameter required file,name,location
                $validated['file'] = $this->fileService->fileUpload($validated['file'], 'downloads_file', 'download/file');
            }
            if (!empty($validated['image'])) {
                $this->fileService->deleteFile($download->image, 'download/image');

                //file uplaod function parameter required file,name,location
                $validated['image'] = $this->fileService->fileUpload($validated['image'], 'downloads_cover_image', 'download/image');
            }



            $download->update($validated);

            return redirect()->route('download.index', [$society, $conference])->with('status', 'File Added Successfully');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($society, $conference, Download $download)
    {
        try {
            $download->update([
                'status' => 0
            ]);
            return redirect()->back()->with('status', 'File Deleted Successfully');
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function changeStatus(Download $download)
    {
        if ($download->is_featured == 1) {
            $status = 0;
        } else {
            $status = 1;
        }

        $download->update(['is_featured' => $status]);

        return redirect()->back()->with('status', 'File featured changed successfully.');
    }
}
