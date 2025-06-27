<?php

namespace App\Http\Controllers\Backend\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\Sponsor\SponsorCategory;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class SponsorCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($society, $conference)
    {
        // $societyDetail = society_detail();

        // if (is_super_admin() && empty($societyDetail)) {
        //     return redirect()->route('dashboard');
        // }
        // if (is_society_admin()) {
        //     $categories = SponsorCategory::where([
        //         'society_id' => current_user()->societies->value('id'),
        //         'status' => 1
        //     ])->latest()->get();
        // } elseif (is_super_admin()) {
        //     $categories = SponsorCategory::where([
        //         'society_id' => $societyDetail->id,
        //         'status' => 1
        //     ])->latest()->get();
        // } else {
        //     return redirect()->route('dashboard');
        // }
        $categories = SponsorCategory::where([
            'society_id' => $society->id,
            'status' => 1
        ])->latest()->get();
        return view('backend.sponsor.category.index', compact('categories', 'society', 'conference'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($society, $conference)
    {
        return view('backend.sponsor.category.create', compact('society', 'conference'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $society, $conference)
    {
        try {
            $validated = $request->validate([
                'category_name' => 'required|unique:sponsor_categories,category_name'
            ]);
            $validated['slug'] = slugify($validated['category_name']);
            $validated['society_id'] = $society->id;

            SponsorCategory::create($validated);

            return redirect()->route('sponsor-category.index', [$society, $conference])->with('status', 'Sponsor Category Added Successfully');
        } catch (Exception $e) {
            throw $e;
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($society, $conference, SponsorCategory $sponsor_category)
    {
        return view('backend.sponsor.category.create', compact('sponsor_category', 'society', 'conference'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $society, $conference, SponsorCategory $sponsor_category)
    {
        try {
            $validated = $request->validate([
                'category_name' => 'required|unique:sponsor_categories,category_name,' . $sponsor_category->id
            ]);
            $validated['slug'] = slugify($validated['category_name']);

            $sponsor_category->update($validated);

            return redirect()->route('sponsor-category.index', [$society, $conference])->with('status', 'Sponsor Category Updated Successfully');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($society, $conference, SponsorCategory $sponsor_category)
    {
        try {
            $sponsor_category->update(['status' => 0]);

            return redirect()->route('sponsor-category.index', [$society, $conference])->with('delete', 'Sponsor Category Deleted Successfully');
        } catch (QueryException $e) {
            return redirect()->back()->with('delete', 'Cannot delete this sponser category.');
        } catch (Exception $e) {
            throw $e;
        }
    }
}
