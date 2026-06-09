<?php

namespace App\Http\Controllers\Backend\Conference;

use App\Http\Controllers\Controller;
use App\Models\Conference\InvitationCategory;
use Illuminate\Http\Request;
use Exception;

class InvitationCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($society, $conference)
    {
        $categories = InvitationCategory::where('conference_id', $conference->id)
            ->ordered()
            ->get();

        return view('backend.conference.invitation-category.index', compact(
            'categories',
            'society',
            'conference'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($society, $conference)
    {
        return view('backend.conference.invitation-category.create', compact(
            'society',
            'conference'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $society, $conference)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'required|boolean',
            ]);

            // Get the next display order
            $maxOrder = InvitationCategory::where('conference_id', $conference->id)
                ->max('display_order') ?? 0;
            $validated['display_order'] = $maxOrder + 1;
            $validated['conference_id'] = $conference->id;

            InvitationCategory::create($validated);

            return redirect()->route('conference.invitation-category.index', [$society, $conference])
                ->with('status', 'Invitation category created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()
                ->with('delete', 'Error creating category: ' . $e->getMessage());
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
    public function edit($society, $conference, InvitationCategory $invitationCategory)
    {
        return view('backend.conference.invitation-category.edit', compact(
            'society',
            'conference',
            'invitationCategory'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $society, $conference, InvitationCategory $invitationCategory)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'required|boolean',
            ]);

            $invitationCategory->update($validated);

            return redirect()->route('conference.invitation-category.index', [$society, $conference])
                ->with('status', 'Invitation category updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()
                ->with('delete', 'Error updating category: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($society, $conference, InvitationCategory $invitationCategory)
    {
        try {
            $invitationCategory->delete();

            return redirect()->back()
                ->with('status', 'Invitation category deleted successfully.');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('delete', 'Error deleting category: ' . $e->getMessage());
        }
    }

    /**
     * Update display order via AJAX
     */
    public function updateOrder(Request $request, $society, $conference)
    {
        try {
            $request->validate([
                'order' => 'required|array',
                'order.*.id' => 'required|integer',
                'order.*.display_order' => 'required|integer',
            ]);

            foreach ($request->order as $item) {
                InvitationCategory::where('id', $item['id'])
                    ->where('conference_id', $conference->id)
                    ->update(['display_order' => $item['display_order']]);
            }

            return response()->json([
                'type' => 'success',
                'message' => 'Order updated successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Error updating order: ' . $e->getMessage()
            ], 500);
        }
    }
}
