<?php

namespace App\Http\Controllers\Backend\Society;

use App\Http\Controllers\Controller;
use App\Models\User\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::whereStatus(1)->get();
        return view('backend.users.department.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.users.department.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $req = $request->validate([
                'name' => 'required'
            ], [
                'name.required' => 'Department Name Field is required'
            ]);

            Department::create($req);
            return redirect()->route('department.index')->with('status', 'Department Created Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        return view('backend.users.department.create', compact('department'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        try {
            $req = $request->validate([
                'name' => 'required'
            ], [
                'name.required' => 'Department Name Field is required'
            ]);

            $department->update($req);
            return redirect()->route('department.index')->with('status', 'Department Updated Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        try {
            $department->update(['status' => 0]);
            return redirect()->route('department.index')->with('status', 'Department Deleted Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }
}
