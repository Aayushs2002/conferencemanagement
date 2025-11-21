<?php

namespace App\Http\Controllers\Backend\Society;

use App\Http\Controllers\Controller;
use App\Models\User\Department;
use App\Models\User\Society;
use Illuminate\Http\Request;
use Exception;

class SocietyDepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($society)
    {
        $departments = Department::where('status', 1)->get();
        $selectedDepartments = $society->departments->pluck('id')->toArray();

        return view('backend.society.department.index', compact('society', 'departments', 'selectedDepartments'));
    }

    /**
     * Update the departments for the society.
     */
    public function update(Request $request, $society)
    {
        try {
            $validated = $request->validate([
                'departments' => 'nullable|array',
                'departments.*' => 'exists:departments,id'
            ]);

            // Sync the selected departments
            $society->departments()->sync($validated['departments'] ?? []);

            return redirect()->route('society.department.index', $society->getRouteKey())
                ->with('status', 'Departments Updated Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }
}
