<?php

namespace App\Http\Controllers\Backend\Cms;

use App\Http\Controllers\Controller;
use App\Models\Cms\Page;
use App\Services\File\FileService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class PageController extends Controller
{
    public function __construct(protected FileService $fileService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pages = Page::whereStatus(1)->get();
        return view('backend.cms.page.index', compact('pages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.cms.page.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $rules = [
                'title' => 'required|unique:pages,title',
                'content' => 'required',
                'image' => 'required|mimes:jpg,png,jpeg',
                'meta_tag' => 'required',
                'meta_description' => 'required',
            ];

            $validated = $request->validate($rules);

            $validated['slug'] = Str::slug($validated['title']);

            if (!empty($validated['image'])) {
                //file uplaod function parameter required file,name,location
                $validated['image'] = $this->fileService->fileUpload($validated['image'], 'page_image', 'page/image');
            }

            Page::create($validated);

            return redirect()->route('page.index')->with('status', 'Page Added Successfully');
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
    public function edit(Page $page)
    {
        return view('backend.cms.page.create', compact('page'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Page $page)
    {
        try {
            $rules = [
                'title' => 'required|unique:features,title,' . $page->id,
                'content' => 'required',
                'image' => 'nullable|mimes:jpg,png,jpeg',
                'meta_tag' => 'required',
                'meta_description' => 'required',
            ];

            $validated = $request->validate($rules);
            $validated['slug'] = Str::slug($validated['title']);

            if (!empty($validated['image'])) {
                $this->fileService->deleteFile($page->image, 'page/image');

                //file uplaod function parameter required file,name,location
                $validated['image'] = $this->fileService->fileUpload($validated['image'], 'page_image', 'page/image');
            }
            $page->update($validated);

            return redirect()->route('page.index')->with('status', 'Page Updated Successfully');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page)
    {
        try {
            $page->update([
                'status' => 0
            ]);
            return redirect()->back()->with('status', 'Page Deleted Successfuly');
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
