<?php

namespace App\Http\Controllers\Backend\Society;

use App\Http\Controllers\Controller;
use App\Models\User\NamePrefix;
use App\Models\User\Society;
use Illuminate\Http\Request;
use Exception;

class SocietyNamePrefixController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($society)
    {
        // $society = Society::findByHashid($society);
        $namePrefixes = NamePrefix::where('status', 1)->get();
        $selectedPrefixes = $society->namePrefixes->pluck('id')->toArray();

        return view('backend.society.name-prefix.index', compact('society', 'namePrefixes', 'selectedPrefixes'));
    }

    /**
     * Update the name prefixes for the society.
     */
    public function update(Request $request, $society)
    {
        try {
            // $society = Society::findByHashid($society);
            
            $validated = $request->validate([
                'name_prefixes' => 'nullable|array',
                'name_prefixes.*' => 'exists:name_prefixes,id'
            ]);

            // Sync the selected name prefixes
            $society->namePrefixes()->sync($validated['name_prefixes'] ?? []);

            return redirect()->route('society.name-prefix.index', $society->getRouteKey())
                ->with('status', 'Name Prefixes Updated Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }
}
