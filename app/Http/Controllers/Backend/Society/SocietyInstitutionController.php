<?php

namespace App\Http\Controllers\Backend\Society;

use App\Http\Controllers\Controller;
use App\Models\User\Institution;
use App\Models\User\Society;
use Illuminate\Http\Request;
use Exception;

class SocietyInstitutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($society)
    {
        $institutions = Institution::where('status', 1)->get();
        $selectedInstitutions = $society->institutions->pluck('id')->toArray();

        return view('backend.society.institution.index', compact('society', 'institutions', 'selectedInstitutions'));
    }

    /**
     * Update the institutions for the society.
     */
    public function update(Request $request, $society)
    {
        try {
            $validated = $request->validate([
                'institutions' => 'nullable|array',
                'institutions.*' => 'exists:institutions,id'
            ]);

            // Sync the selected institutions
            $society->institutions()->sync($validated['institutions'] ?? []);

            return redirect()->route('society.institution.index', $society->getRouteKey())
                ->with('status', 'Institutions Updated Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }
}
