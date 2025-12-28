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
        $departments = Department::where('status', 1)->orderBy('name', 'ASC')->get();
        $selectedDepartments = $society->departments()
            ->orderBy('society_department.display_order', 'asc')
            ->orderBy('society_department.department_id', 'asc')
            ->get();

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

            $departments = $validated['departments'] ?? [];
            $syncData = [];
            
            // Get max order (excluding nulls)
            $maxOrder = $society->departments()
                ->whereNotNull('society_department.display_order')
                ->max('society_department.display_order') ?? 0;
            
            foreach ($departments as $index => $departmentId) {
                $existing = $society->departments()->where('department_id', $departmentId)->first();
                
                if ($existing && $existing->pivot->display_order !== null) {
                    $syncData[$departmentId] = ['display_order' => $existing->pivot->display_order];
                } else {
                    $maxOrder++;
                    $syncData[$departmentId] = ['display_order' => $maxOrder];
                }
            }

            $society->departments()->sync($syncData);

            return redirect()->route('society.department.index', $society->getRouteKey())
                ->with('status', 'Departments Updated Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    public function updateOrder(Request $request, $society)
    {
        try {
            $orders = $request->orders;
            
            foreach ($orders as $order) {
                $society->departments()->updateExistingPivot($order['id'], [
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
