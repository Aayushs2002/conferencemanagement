<?php

namespace App\Http\Controllers\Backend\Cms;

use App\Http\Controllers\Controller;
use App\Models\Cms\Feature;
use App\Services\File\FileService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FeatureController extends Controller
{
    public function __construct(protected FileService $fileService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $features = Feature::whereStatus(1)->get();
        return view('backend.cms.feature.index', compact('features'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.cms.feature.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $rules = [
                'title' => 'required|unique:features,title',
                'image' => 'required|mimes:jpg,png,jpeg',
                'description' => 'required'
            ];

            $validated = $request->validate($rules);

            $validated['slug'] = Str::slug($validated['title']);

            if (!empty($validated['image'])) {
                //file uplaod function parameter required file,name,location
                $validated['image'] = $this->fileService->fileUpload($validated['image'], 'feature_image', 'feature/image');
            }

            Feature::create($validated);

            return redirect()->route('feature.index')->with('status', 'Feature Added Successfully');
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
    public function edit(Feature $feature)
    {
        
        return view('backend.cms.feature.create', compact('feature'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Feature $feature)
    {
        try {
            $rules = [
                'title' => 'required|unique:features,title,' . $feature->id,
                'image' => 'nullable|mimes:jpg,png,jpeg',
                'description' => 'required'
            ];

            $validated = $request->validate($rules);
            $validated['slug'] = Str::slug($validated['title']);

            if (!empty($validated['image'])) {
                $this->fileService->deleteFile($feature->image, 'feature/image');

                //file uplaod function parameter required file,name,location
                $validated['image'] = $this->fileService->fileUpload($validated['image'], 'feature_image', 'feature/image');
            }
            $feature->update($validated);

            return redirect()->route('feature.index')->with('status', 'Feature Updated Successfully');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Feature $feature)
    {
        try {
            $feature->update([
                'status' => 0
            ]);
            return redirect()->back()->with('status', 'Feature Deleted Successfuly');
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
