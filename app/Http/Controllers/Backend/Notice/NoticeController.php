<?php

namespace App\Http\Controllers\Backend\Notice;

use App\Http\Controllers\Controller;
use App\Models\Notice\Notice;
use App\Services\File\FileService;
use Exception;
use Illuminate\Http\Request;

class NoticeController extends Controller
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
        $notices = Notice::where(['conference_id' => $conference->id, 'status' => 1])->get();
        return view('backend.notice.index', compact('notices', 'society', 'conference'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($society, $conference)
    {
        return view('backend.notice.create', compact('society', 'conference'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $society, $conference)
    {
        try {
            $rules = [
                'title' => 'required|unique:notices,title',
                'date' => 'required|date',
                'attachment' => 'nullable|mimes:jpg,png,pdf,doc,docs,xlsx|max:5120',
                'image' => 'nullable|mimes:jpg,png',
                'description' => 'nullable'
            ];

            $validated = $request->validate($rules);
            if (!empty($validated['attachment'])) {
                //file uplaod function parameter required file,name,location
                $validated['attachment'] = $this->fileService->fileUpload($validated['attachment'], 'attachment', 'notice/attachment');
            }
            if (!empty($validated['image'])) {
                //file uplaod function parameter required file,name,location
                $validated['image'] = $this->fileService->fileUpload($validated['image'], 'notice_cover_image', 'notice/image');
            }


            $validated['conference_id'] = $conference->id;

            Notice::create($validated);

            return redirect()->route('notice.index', [$society, $conference])->with('status', 'News/notice Added Successfully');
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function show(Request $request)
    {
        $notice = Notice::whereId($request->id)->first();
        return view('backend.notice.show', compact('notice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($society, $conference, Notice $notice)
    {
        return view('backend.notice.create', compact('notice', 'society', 'conference'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $society, $conference, Notice $notice)
    {
        try {
            $rules = [
                'title' => 'required|unique:downloads,title,' . $notice->id,
                'date' => 'required|date',
                'attachment' => 'nullable|mimes:jpg,png,pdf,doc,docs,xlsx|max:5120',
                'image' => 'nullable|mimes:jpg,png',
                'description' => 'nullable'
            ];

            $validated = $request->validate($rules);
            if (!empty($validated['attachment'])) {
                $this->fileService->deleteFile($notice->attachment, 'notice/attachment');

                //file uplaod function parameter required file,name,location
                $validated['attachment'] = $this->fileService->fileUpload($validated['attachment'], 'notice_attachment', 'notice/attachment');
            }
            if (!empty($validated['image'])) {
                $this->fileService->deleteFile($notice->image, 'notice/image');

                //file uplaod function parameter required file,name,location
                $validated['image'] = $this->fileService->fileUpload($validated['image'], 'notice_cover_image', 'notice/image');
            }



            $notice->update($validated);

            return redirect()->route('notice.index', [$society, $conference])->with('status', 'News/notice Added Successfully');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($society, $conference, Notice $notice)
    {
        try {
            $notice->update([
                'status' => 0
            ]);
            return redirect()->back()->with('status', 'News/Notice Deleted Successfuly');
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function changeFeatured(Notice $notice)
    {
        if ($notice->is_featured == 1) {
            $isFeatured = 0;
        } else {
            $isFeatured = 1;
        }

        $notice->update(['is_featured' => $isFeatured]);

        return redirect()->back()->with('status', 'Notice featured status changed successfully.');
    }
}
