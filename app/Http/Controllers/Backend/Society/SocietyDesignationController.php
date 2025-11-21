<?php

namespace App\Http\Controllers\Backend\Society;

use App\Http\Controllers\Controller;
use App\Models\User\Designation;
use App\Models\User\Society;
use Illuminate\Http\Request;
use Exception;

class SocietyDesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($society)
    {
        $designations = Designation::where('status', 1)->get();
        $selectedDesignations = $society->designations->pluck('id')->toArray();

        return view('backend.society.designation.index', compact('society', 'designations', 'selectedDesignations'));
    }

    /**
     * Update the designations for the society.
     */
    public function update(Request $request, $society)
    {
        try {
            $validated = $request->validate([
                'designations' => 'nullable|array',
                'designations.*' => 'exists:designations,id'
            ]);

            // Sync the selected designations
            $society->designations()->sync($validated['designations'] ?? []);

            return redirect()->route('society.designation.index', $society->getRouteKey())
                ->with('status', 'Designations Updated Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }
}
