<?php

namespace App\Http\Controllers\Backend\Cms;

use App\Http\Controllers\Controller;
use App\Models\Cms\Blog;
use App\Services\File\FileService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class BlogController extends Controller
{

    public function __construct(protected FileService $fileService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    
        $blogs = Blog::whereStatus(1)->get();
        return view('backend.cms.blog.index', compact('blogs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.cms.blog.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $rules = [
                'title' => 'required|unique:blogs,title',
                'image' => 'required|mimes:jpg,png,jpeg',
                'description' => 'required'
            ];

            $validated = $request->validate($rules);

            $validated['slug'] = Str::slug($validated['title']);

            if (!empty($validated['image'])) {
                //file uplaod function parameter required file,name,location
                $validated['image'] = $this->fileService->fileUpload($validated['image'], 'blog_image', 'blog/image');
            }

            Blog::create($validated);

            return redirect()->route('blog.index')->with('status', 'Blog Added Successfully');
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
    public function edit(Blog $blog)
    {
        return view('backend.cms.blog.create', compact('blog'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Blog $blog)
    {
        try {
            $rules = [
                'title' => 'required|unique:blogs,title,' . $blog->id,
                'image' => 'nullable|mimes:jpg,png,jpeg',
                'description' => 'required'
            ];

            $validated = $request->validate($rules);
            $validated['slug'] = Str::slug($validated['title']);

            if (!empty($validated['image'])) {
                $this->fileService->deleteFile($blog->image, 'blog/image');

                //file uplaod function parameter required file,name,location
                $validated['image'] = $this->fileService->fileUpload($validated['image'], 'blog_image', 'blog/image');
            }
            $blog->update($validated);

            return redirect()->route('blog.index')->with('status', 'Blog Updated Successfully');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        try {
            $blog->update([
                'status' => 0
            ]);
            return redirect()->back()->with('status', 'Blog Deleted Successfuly');
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
