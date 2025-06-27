<?php

namespace App\Http\Controllers\Backend\Faq;

use App\Http\Controllers\Controller;
use App\Models\Faq\FaqCategory;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class FaqCategoryController extends Controller
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
        //     $faq_categories = FaqCategory::where([
        //         'society_id' => current_user()->societies->value('id'),
        //         'status' => 1
        //     ])->latest()->get();
        // } elseif (is_super_admin()) {
        //     $faq_categories = FaqCategory::where([
        //         'society_id' => $societyDetail->id,
        //         'status' => 1
        //     ])->latest()->get();
        // } else {
        //     return redirect()->route('dashboard');
        // }
        $faq_categories = FaqCategory::where([
            'society_id' => $society->id,
            'status' => 1
        ])->latest()->get();
        return view('backend.faq.category.index', compact('faq_categories', 'society', 'conference'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($society, $conference)
    {
        return view('backend.faq.category.create', compact('society', 'conference'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $society, $conference)
    {
        try {
            $validated = $request->validate([
                'category_name' => 'required|unique:faq_categories,category_name'
            ]);
            $validated['slug'] = slugify($validated['category_name']);
            $validated['society_id'] = $society->id;

            FaqCategory::create($validated);

            return redirect()->route('faq-category.index', [$society, $conference])->with('status', 'Faq Category Added Successfully');
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
    public function edit($society, $conference, FaqCategory $faq_category)
    {
        return view('backend.faq.category.create', compact('faq_category', 'society', 'conference'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $society, $conference, FaqCategory $faq_category)
    {
        try {
            $validated = $request->validate([
                'category_name' => 'required|unique:faq_categories,category_name,' . $faq_category->id
            ]);
            $validated['slug'] = slugify($validated['category_name']);

            $faq_category->update($validated);

            return redirect()->route('faq-category.index', [$society, $conference])->with('status', 'Faq Category Updated Successfully');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($society, $conference, FaqCategory $faq_category)
    {
        try {
            $faq_category->update(['status' => 0]);

            return redirect()->route('faq-category.index', [$society, $conference])->with('delete', 'Faq Category Deleted Successfully');
        } catch (QueryException $e) {
            return redirect()->back()->with('delete', 'Cannot delete this sponser category.');
        } catch (Exception $e) {
            throw $e;
        }
    }
}
