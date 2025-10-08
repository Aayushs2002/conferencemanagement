<?php

namespace App\Http\Controllers\Backend\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\User\Department;
use App\Models\User\Designation;
use App\Models\User\Institution;
use App\Models\User\MemberType;
use App\Models\User\NamePrefix;
use App\Models\User\Society;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
            if (current_user()->type == 1) {
                $user = User::where('id', $request->user_id)->first();
            } else {
                $user = current_user();
            }

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

    public function checkCouncilMembership(Request $request)
    {
        $user = auth()->user();
        $userCouncilNum = $user->userDetail->council_number ?? null;
        $userDob = $user->userDetail->dob_ad ?? null;

        if (!$userCouncilNum) {
            return response()->json([
                'isMember' => false,
                'error' => 'Your council number is missing.'
            ]);
        }

        $memberType = MemberType::find($request->member_type_id)->type ?? null;

        if (!$memberType) {
            return response()->json([
                'isMember' => false,
                'error' => 'Invalid member type selected.'
            ]);
        }

        $apiResponse = Http::get('https://membership.san.org.np/api/updated-members');

        if (!$apiResponse->successful()) {
            return response()->json([
                'isMember' => false,
                'error' => 'Could not fetch membership data from external API.'
            ]);
        }

        $apiData = $apiResponse->json();

        $matchByCouncil = collect($apiData)->firstWhere('councilNum', $userCouncilNum);

        if (!$matchByCouncil) {
            return response()->json([
                'isMember' => false,
                'error' => 'Council number not found in SAN membership records.'
            ]);
        }

        if ($matchByCouncil['memberType'] !== $memberType) {
            return response()->json([
                'isMember' => false,
                'error' => 'Member type does not match SAN records.'
            ]);
        }

        if ($matchByCouncil['dobAD'] !== $userDob) {
            return response()->json([
                'isMember' => false,
                'error' => 'Date of birth does not match SAN records.'
            ]);
        }

        return response()->json([
            'isMember' => true,
            'message' => 'You are verified as a SAN member.'
        ]);
    }
}
