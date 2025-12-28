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
        $designations = Designation::where('status', 1)->orderBy('designation', 'ASC')->get();
        $selectedDesignations = $society->designations()
            ->orderBy('society_designation.display_order', 'asc')
            ->orderBy('society_designation.designation_id', 'asc')
            ->get();

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

            $designations = $validated['designations'] ?? [];
            $syncData = [];
            
            // Get max order (excluding nulls)
            $maxOrder = $society->designations()
                ->whereNotNull('society_designation.display_order')
                ->max('society_designation.display_order') ?? 0;
            
            foreach ($designations as $index => $designationId) {
                $existing = $society->designations()->where('designation_id', $designationId)->first();
                
                if ($existing && $existing->pivot->display_order !== null) {
                    $syncData[$designationId] = ['display_order' => $existing->pivot->display_order];
                } else {
                    $maxOrder++;
                    $syncData[$designationId] = ['display_order' => $maxOrder];
                }
            }

            $society->designations()->sync($syncData);

            return redirect()->route('society.designation.index', $society->getRouteKey())
                ->with('status', 'Designations Updated Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error: ' . $e->getMessage());
        }
    }

    public function updateOrder(Request $request, $society)
    {
        try {
            $orders = $request->orders;
            
            foreach ($orders as $order) {
                $society->designations()->updateExistingPivot($order['id'], [
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
