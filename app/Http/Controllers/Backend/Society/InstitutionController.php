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
        $institutions = Institution::whereStatus(1)->orderBy('name', 'ASC')->get();
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

    /**
     * Show the merge form
     */
    public function mergeForm(Request $request)
    {
        $institution = Institution::findOrFail($request->id);
        
        // Get all other active institutions except the current one
        $institutions = Institution::where('status', 1)
            ->where('id', '!=', $institution->id)
            ->orderBy('name', 'ASC')
            ->get();
            
        return view('backend.users.institution.merge-institution', compact('institution', 'institutions'));
    }

    /**
     * Process the merge
     */
    public function mergeSubmit(Request $request)
    {
        try {
            $validated = $request->validate([
                'institution_id' => 'required|exists:institutions,id',
                'second_institution_id' => 'required|exists:institutions,id|different:institution_id',
            ], [
                'second_institution_id.required' => 'Please select an institution to merge.',
                'second_institution_id.different' => 'Cannot merge an institution with itself.',
            ]);

            $mainInstitution = Institution::findOrFail($request->institution_id);
            $secondInstitution = Institution::findOrFail($request->second_institution_id);

            \DB::beginTransaction();

            // Update all user_details records to point to the main institution
            \DB::table('user_details')
                ->where('institution_id', $secondInstitution->id)
                ->update(['institution_id' => $mainInstitution->id]);

            // Delete the second institution
            $secondInstitution->delete();

            \DB::commit();

            return response()->json([
                'type' => 'success',
                'message' => 'Institution merged successfully. All users have been transferred to ' . $mainInstitution->name
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'type' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
