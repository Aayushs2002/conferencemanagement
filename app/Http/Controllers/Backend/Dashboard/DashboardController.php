<?php

namespace App\Http\Controllers\Backend\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User\Department;
use App\Models\User\Designation;
use App\Models\User\Institution;
use App\Models\User\MemberType;
use App\Models\User\NamePrefix;
use App\Models\User\Society;
use Exception;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $joinedSocities = current_user()->societies;
        $societyCount = Society::where('status', 1)->count();
        $namePrfixCount = NamePrefix::where('status', 1)->count();
        $intitutionCount = Institution::where('status', 1)->count();
        $designationCount = Designation::where('status', 1)->count();
        $departmentCount = Department::where('status', 1)->count();
        return view('backend.dashboard.index', compact('joinedSocities', 'societyCount', 'namePrfixCount', 'intitutionCount', 'designationCount', 'departmentCount'));
    }

    public function joinSociety(Request $request)
    {
        $joinedSocietyIds = current_user()->societies->pluck('id')->toArray();

        $societies = Society::where('status', 1)
            ->whereNotIn('id', $joinedSocietyIds)
            ->get();
        return view('backend.dashboard.join-society-model', compact('societies'));
    }

    public function getMemberType(Request $request)
    {
        try {
            $user = current_user();
            $society_id = $request->society_id;

            if (!$society_id) {
                return response()->json(['type' => 'error', 'message' => 'Society ID is required.', 'data' => []]);
            }

            if ($user->userDetail->country_id == 125) {
                $types = MemberType::where([
                    'delegate' => 1,
                    'society_id' => $society_id,
                    'status' => 1
                ])->get();
            } else {
                $types = MemberType::where([
                    'delegate' => 2,
                    'society_id' => $society_id,
                    'status' => 1
                ])->get();
            }

            return response()->json([
                'type' => 'success',
                'message' => 'Member types fetched successfully.',
                'data' => $types
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Something went wrong.',
                'data' => []
            ]);
        }
    }
}
