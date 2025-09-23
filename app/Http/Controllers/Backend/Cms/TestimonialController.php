<?php

namespace App\Http\Controllers\Backend\Cms;

use App\Http\Controllers\Controller;
use App\Models\Cms\Testimonial;
use App\Services\File\FileService;
use Exception;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function __construct(protected FileService $fileService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $testimonials = Testimonial::whereStatus(1)->get();
        return view('backend.cms.testimonial.index', compact('testimonials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.cms.testimonial.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        try {
            $rules = [
                'name' => 'required',
                'image' => 'required|mimes:jpg,png,jpeg',
                'designation' => 'required',
                'organization_name' => 'required',
                'description' => 'required',
                'rating' => 'required|integer|min:1|max:5',
            ];

            $validated = $request->validate($rules);


            if (!empty($validated['image'])) {
                //file uplaod function parameter required file,name,location
                $validated['image'] = $this->fileService->fileUpload($validated['image'], 'testimonial_image', 'testimonial/image');
            }

            Testimonial::create($validated);

            return redirect()->route('testimonial.index')->with('status', 'Feature Added Successfully');
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
    public function edit(Testimonial $testimonial)
    {
        return view('backend.cms.testimonial.create', compact('testimonial'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Testimonial $testimonial)
    {
        try {
            $rules = [
                'name' => 'required',
                'image' => 'nullable|mimes:jpg,png,jpeg',
                'designation' => 'required',
                'organization_name' => 'required',
                'description' => 'required',
                'rating' => 'required|integer|min:1|max:5',
            ];

            $validated = $request->validate($rules);

            if (!empty($validated['image'])) {
                $this->fileService->deleteFile($testimonial->image, 'testimonial/image');

                //file uplaod function parameter required file,name,location
                $validated['image'] = $this->fileService->fileUpload($validated['image'], 'testimonial_image', 'testimonial/image');
            }
            $testimonial->update($validated);

            return redirect()->route('testimonial.index')->with('status', 'Testimonial Updated Successfully');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Testimonial $testimonial)
    {
        try {
            $testimonial->update([
                'status' => 0
            ]);
            return redirect()->back()->with('status', 'Testimonial Deleted Successfuly');
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
