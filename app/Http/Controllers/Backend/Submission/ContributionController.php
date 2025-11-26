<?php

namespace App\Http\Controllers\Backend\Submission;

use App\Http\Controllers\Controller;
use App\Models\Conference\Conference;
use App\Models\Conference\Contribution;
use Exception;
use Illuminate\Http\Request;

class ContributionController extends Controller
{
    public function index(Request $request, $society, $conference)
    {
        $contributions = Contribution::where([
            'conference_id' => $conference->id,
            'status' => 1
        ])->orderBy('id', 'desc')->get();

        return view('backend.submission.contribution.index', compact('society', 'conference', 'contributions'));
    }

    public function create(Request $request, $society, $conference)
    {
        $contribution = null;
        if ($request->has('id')) {
            $contribution = Contribution::where('id', $request->id)->first();
        }

        return view('backend.submission.contribution.create', compact('society', 'conference', 'contribution'));
    }

    public function store(Request $request, $society, $conference)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            $validated['conference_id'] = $conference->id;
            $validated['status'] = 1;

            Contribution::create($validated);

            return redirect()->route('contribution.index', [$society, $conference])
                ->with('status', 'Contribution created successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    public function update(Request $request, $society, $conference, $contribution)
    {
        // dd($contribution);
        $contribution = Contribution::where('id', $contribution)->first();
        // dd($contribution);
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            $contribution->update($validated);

            return redirect()->route('contribution.index', [$society, $conference])
                ->with('status', 'Contribution updated successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    public function destroy($society, $conference, $contribution)
    {
        try {
        $contribution = Contribution::where('id', $contribution)->first();

            $contribution->update(['status' => 0]);

            return redirect()->back()->with('status', 'Contribution deleted successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }
}
