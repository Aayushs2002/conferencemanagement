<?php

namespace App\Http\Controllers\Backend\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\Sponsor\SponsorCategory;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class SponsorCategoryController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index($society, $conference)
    {
        $categories = SponsorCategory::where([
            'society_id' => $society->id,
            'status' => 1
        ])->orderBy('display_order', 'asc')->get();
        return view('backend.sponsor.category.index', compact('categories', 'society', 'conference'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($society, $conference)
    {
        return view('backend.sponsor.category.create', compact('society', 'conference'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $society, $conference)
    {
        try {
            $validated = $request->validate([
                'category_name' => 'required'
            ]);
            $validated['slug'] = slugify($validated['category_name']);
            $validated['society_id'] = $society->id;

            SponsorCategory::create($validated);

            return redirect()->route('sponsor-category.index', [$society, $conference])->with('status', 'Sponsor Category Added Successfully');
        } catch (Exception $e) {
            throw $e;
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($society, $conference, SponsorCategory $sponsor_category)
    {
        $this->authorize('edit', $sponsor_category);

        return view('backend.sponsor.category.create', compact('sponsor_category', 'society', 'conference'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $society, $conference, SponsorCategory $sponsor_category)
    {
        try {
            $validated = $request->validate([
                'category_name' => 'required'
            ]);
            $validated['slug'] = slugify($validated['category_name']);

            $sponsor_category->update($validated);

            return redirect()->route('sponsor-category.index', [$society, $conference])->with('status', 'Sponsor Category Updated Successfully');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($society, $conference, SponsorCategory $sponsor_category)
    {
        try {
            $sponsor_category->update(['status' => 0]);

            return redirect()->route('sponsor-category.index', [$society, $conference])->with('delete', 'Sponsor Category Deleted Successfully');
        } catch (QueryException $e) {
            return redirect()->back()->with('delete', 'Cannot delete this sponser category.');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Update display order of sponsor categories.
     */
    public function updateOrder(Request $request, $society, $conference)
    {
        try {
            $orders = $request->orders;
            
            foreach ($orders as $order) {
                SponsorCategory::where('id', $order['id'])->update([
                    'display_order' => $order['position']
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order: ' . $e->getMessage()
            ], 500);
        }
    }
}
