<?php

namespace App\Http\Controllers\Backend\User;

use App\Http\Controllers\Controller;
use App\Models\User\ConferenceUserPassDesignation;
use Illuminate\Http\Request;

class ConferenceUserPassDesignationController extends Controller
{
    /**
     * Delete all ConferenceUserPassDesignation records
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function deleteAll(Request $request)  
    {
        try {
            // Delete all records
            $deletedCount = ConferenceUserPassDesignation::query()->delete();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Successfully deleted {$deletedCount} pass designation(s).",
                    'deleted_count' => $deletedCount
                ]);
            }

            return redirect()->back()->with('success', "Successfully deleted {$deletedCount} pass designation(s).");
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete pass designations: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to delete pass designations: ' . $e->getMessage());
        }
    }
}
