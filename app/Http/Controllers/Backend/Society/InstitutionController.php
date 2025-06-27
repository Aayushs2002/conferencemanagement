<?php

namespace App\Http\Controllers\Backend\Society;

use App\Http\Controllers\Controller;
use App\Models\User\Institution;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $institutions = Institution::whereStatus(1)->get();
        return view('backend.users.institution.index', compact('institutions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.users.institution.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $req = $request->validate([
                'name' => "required"
            ], [
                'name.required' => "Institution Name Field is Required"
            ]);
            Institution::create($req);
            return redirect()->route('institution.index')->with('status', 'Institution Added Successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Institution $institution)
    {
        return view('backend.users.institution.create', compact('institution'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Institution $institution)
    {
        try {
            $req = $request->validate([
                'name' => "required"
            ], [
                'name.required' => "Institution Name Field is Required"
            ]);
            $institution->update($req);
            return redirect()->route('institution.index')->with('status', 'Institution Updated Successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Institution $institution)
    {
        try {
            $institution->update(['status' => 0]);
            return redirect()->route('institution.index')->with('status', 'Institution Deleted Successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }
}
