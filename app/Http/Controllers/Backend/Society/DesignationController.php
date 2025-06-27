<?php

namespace App\Http\Controllers\Backend\Society;

use App\Http\Controllers\Controller;
use App\Models\User\Designation;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $designations = Designation::whereStatus(1)->get();
        return view('backend.users.designation.index', compact('designations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.users.designation.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $req = $request->validate([
                'designation' => 'required'
            ]);

            Designation::create($req);
            return redirect()->route('designation.index')->with('status', 'Designation Created SuccessFully');
        } catch (\Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Designation $designation)
    {
        return view('backend.users.designation.create', compact('designation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Designation $designation)
    {
        try {
            $req = $request->validate([
                'designation' => 'required'
            ]);
            $designation->update($req);
            return redirect()->route('designation.index')->with('status', 'Designation Updated SuccessFully');
        } catch (\Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Designation $designation)
    {
        try {
            $designation->update(['status' => 0]);
            return redirect()->route('designation.index')->with('status', 'Designation Deleted SuccessFully');
        } catch (\Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }
}
