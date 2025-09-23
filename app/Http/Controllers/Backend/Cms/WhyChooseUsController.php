<?php

namespace App\Http\Controllers\Backend\Cms;

use App\Http\Controllers\Controller;
use App\Models\Cms\WhyChooseUs;
use App\Services\File\FileService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class WhyChooseUsController extends Controller
{
    public function __construct(protected FileService $fileService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $whyChooseUs = WhyChooseUs::whereStatus(1)->get();
        return view('backend.cms.why-choose-us.index', compact('whyChooseUs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.cms.why-choose-us.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $rules = [
                'title' => 'required|unique:why_choose_us,title',
                'image' => 'required|mimes:jpg,png,jpeg',
                'description' => 'required'
            ];

            $validated = $request->validate($rules);

            $validated['slug'] = Str::slug($validated['title']);

            if (!empty($validated['image'])) {
                //file uplaod function parameter required file,name,location
                $validated['image'] = $this->fileService->fileUpload($validated['image'], 'why_choose_us_image', 'whyChooseUs/image');
            }

            WhyChooseUs::create($validated);

            return redirect()->route('why-choose-us.index')->with('status', 'Why Choose Us Added Successfully');
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
    public function edit($id)
    {
        $whyChoose = WhyChooseUs::where('id', $id)->first();
        // dd($id);
        return view('backend.cms.why-choose-us.create', compact('whyChoose'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $whyChoose = WhyChooseUs::where('id', $id)->first();

            $rules = [
                'title' => 'required|unique:why_choose_us,title,' . $whyChoose->id,
                'image' => 'nullable|mimes:jpg,png,jpeg',
                'description' => 'required'
            ];

            $validated = $request->validate($rules);
            $validated['slug'] = Str::slug($validated['title']);

            if (!empty($validated['image'])) {
                $this->fileService->deleteFile($whyChoose->image, 'whyChooseUs/image');

                //file uplaod function parameter required file,name,location
                $validated['image'] = $this->fileService->fileUpload($validated['image'], 'why_choose_us_image', 'whyChooseUs/image');
            }
            $whyChoose->update($validated);

            return redirect()->route('why-choose-us.index')->with('status', 'Why Choose Us Updated Successfully');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $whyChoose = WhyChooseUs::where('id', $id)->first();

            $whyChoose->update([
                'status' => 0
            ]);
            return redirect()->back()->with('status', 'Why Choose Us Deleted Successfuly');
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
