<?php

namespace App\Http\Controllers\Backend\Committee;

use App\Http\Controllers\Controller;
use App\Models\Committee\Committee;
use Exception;
use Illuminate\Http\Request;

class CommitteeController extends Controller
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
        //     $committees = Committee::where([
        //         'society_id' => current_user()->societies->value('id'),
        //         'status' => 1
        //     ])->latest()->get();
        // } elseif (is_super_admin()) {
        //     $committees = Committee::where([
        //         'society_id' => $societyDetail->id,
        //         'status' => 1
        //     ])->latest()->get();
        // } else {
        //     return redirect()->route('dashboard');
        // }

        $committees = Committee::where([
            'society_id' => $society->id,
            'status' => 1
        ])->latest()->get();
        return view('backend.committee.committee.index', compact('committees', 'society', 'conference'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($society, $conference)
    {
        return view('backend.committee.committee.create', compact('society', 'conference'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $society, $conference)
    {
        try {
            $validated = $request->validate([
                'committee_name' => 'required|string|unique:committees,committee_name',
                'focal_person' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required',
                'description' => 'nullable'
            ]);
            $validated['society_id'] = $conference->id;
            $validated['slug'] = slugify($validated['committee_name']);

            Committee::create($validated);

            return redirect()->route('committee.index', [$society, $conference])->with('status', 'Committee Added Successfully');
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
    public function edit($society, $conference, Committee $committee)
    {
        return view('backend.committee.committee.create', compact('committee', 'society', 'conference'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $society, $conference, Committee $committee)
    {
        try {
            $validated = $request->validate([
                'committee_name' => 'required|string|unique:committees,committee_name,' . $committee->id,
                'focal_person' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required',
                'description' => 'nullable'
            ]);
            $validated['slug'] = slugify($validated['committee_name']);

            $committee->update($validated);

            return redirect()->route('committee.index', [$society, $conference])->with('status', 'Committee Updated Successfully');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($society, $conference, Committee $committee)
    {
        $committee->update(['status' => 0]);

        return redirect()->route('committee.index', [$society, $conference])->with('delete', 'Committee Deleted Successfully');
    }
}
