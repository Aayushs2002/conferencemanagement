<?php

namespace App\Http\Controllers\Backend\ScientificSession;

use App\Http\Controllers\Controller;
use App\Models\Conference\ScientificSessionCategory;
use Exception;
use Illuminate\Http\Request;

class ScientificSessionCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($society, $conference)
    {
        // $conferenceDetail = conference_detail();

        // if (empty($conferenceDetail)) {
        //     return redirect()->route('dashboard');
        // } 
        $parent_id = request('category') ?? 0;

        $parent_id = request('category') ?? 0;

        $categories = ScientificSessionCategory::where(function ($query) use ($conference, $parent_id) {
            $query->where('status', 1)
                ->where('parent_id', $parent_id)
                ->where(function ($q) use ($conference) {
                    $q->whereNull('conference_id')
                        ->orWhere('conference_id', $conference->id);
                });
        })->get();


        // dd($categories);
        return view('backend.schedule-plan.scientific-session-category.index', compact('categories', 'society', 'conference'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($society, $conference)
    {
        return view('backend.schedule-plan.scientific-session-category.create', compact('society', 'conference'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $society, $conference)
    {
        try {
            $validated = $request->validate([
                'category_name' => 'required',
                'parent_id' => 'required'
            ]);
            $validated['slug'] = slugify($validated['category_name']);
            $validated['conference_id'] = $conference->id;
            ScientificSessionCategory::create($validated);
            if ($request->parent_id) {
                return redirect()->route('category.index', [$society, $conference, 'category' => $request->parent_id])->with('status', 'Scientific Sub Category Created SuccessFully');
            } else {
                return redirect()->route('category.index', [$society, $conference])->with('status', 'Scientific Category Created SuccessFully');
            }
        } catch (Exception $e) {
            //throw $th;
            return redirect()->back()->with('delete', 'Internal Server Error');
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
    public function edit($society, $conference, ScientificSessionCategory $category)
    {
        return view('backend.schedule-plan.scientific-session-category.create', compact('category', 'society', 'conference'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $society, $conference, ScientificSessionCategory $category)
    {
        try {
            $validated = $request->validate([
                'category_name' => 'required',

            ]);
            $validated['slug'] = slugify($validated['category_name']);

            $category->update($validated);
            if ($category->parent_id) {
                return redirect()->route('category.index', [$society, $conference, 'category' => $category->parent_id])->with('status', 'Scientific Sub Category Updated SuccessFully');
            } else {
                return redirect()->route('category.index', [$society, $conference])->with('status', 'Scientific Category Updated SuccessFully');
            }
        } catch (Exception $e) {
            //throw $th;
            dd($e);
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($society, $conference, ScientificSessionCategory $category)
    {
        try {
            $category->update([
                'status' => 0
            ]);
            return redirect()->route('category.index', [$society, $conference])->with('status', 'Scientific Category Deleted SuccessFully');
        } catch (Exception $e) {
            //throw $th;
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }
}
