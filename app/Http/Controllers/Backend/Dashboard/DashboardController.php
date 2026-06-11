<?php

namespace App\Http\Controllers\Backend\Dashboard;

use App\Http\Controllers\Controller;
use App\Imports\DoctorsImport;
use App\Models\User;
use App\Models\User\Department;
use App\Models\User\Designation;
use App\Models\User\Institution;
use App\Models\User\MemberType;
use App\Models\User\NamePrefix;
use App\Models\User\Society;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $joinedSocities = current_user()->societies;
            $societyCount = Society::where('status', 1)->count();
            $namePrfixCount = NamePrefix::where('status', 1)->count();
            $intitutionCount = Institution::where('status', 1)->count();
            $designationCount = Designation::where('status', 1)->count();
            $departmentCount = Department::where('status', 1)->count();

            return view('backend.dashboard.index', compact('joinedSocities', 'societyCount', 'namePrfixCount', 'intitutionCount', 'designationCount', 'departmentCount'));
        } catch (\Exception $e) {
            // throw $th;
            // Log::channel('sentry')->error('Dashboard Load Error: ' . $e->getMessage());
            return $e->getMessage();
        }
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
            if (! $society_id) {
                return response()->json(['type' => 'error', 'message' => 'Society ID is required.', 'data' => []]);
            }

            if ($user->userDetail->country_id == 125) {
                $types = MemberType::where([
                    'delegate' => 1,
                    'society_id' => $society_id,
                    'status' => 1,
                ])->get();
            } else {
                $types = MemberType::where([
                    'delegate' => 2,
                    'society_id' => $society_id,
                    'status' => 1,
                ])->get();
            }

            return response()->json([
                'type' => 'success',
                'message' => 'Member types fetched successfully.',
                'data' => $types,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Something went wrong.',
                'data' => [],
            ]);
        }
    }

    // public function checkCouncilMembership(Request $request)
    // {
    //     $user = auth()->user();
    //     $userCouncilNum = $user->userDetail->council_number ?? null;
    //     $userDob = $user->userDetail->dob_ad ?? null;

    //     if (! $userCouncilNum) {
    //         return response()->json([
    //             'isMember' => false,
    //             'error' => 'Your council number is missing.',
    //         ]);
    //     }

    //     $memberType = MemberType::find($request->member_type_id)->type ?? null;

    //     if (! $memberType) {
    //         return response()->json([
    //             'isMember' => false,
    //             'error' => 'Invalid member type selected.',
    //         ]);
    //     }

    //     // $apiResponse = Http::get('https://membership.san.org.np/api/updated-members');

    //     // if (!$apiResponse->successful()) {
    //     //     return response()->json([
    //     //         'isMember' => false,
    //     //         'error' => 'Could not fetch membership data from external API.'
    //     //     ]);
    //     // }

    //     // $apiData = $apiResponse->json();

    //     // $matchByCouncil = collect($apiData)->firstWhere('councilNum', $userCouncilNum);

    //     // if (!$matchByCouncil) {
    //     //     return response()->json([
    //     //         'isMember' => false,
    //     //         'error' => 'Council number not found in SAN membership records.'
    //     //     ]);
    //     // }

    //     // if ($matchByCouncil['memberType'] !== $memberType) {
    //     //     return response()->json([
    //     //         'isMember' => false,
    //     //         'error' => 'Member type does not match SAN records.'
    //     //     ]);
    //     // }

    //     // if ($matchByCouncil['dobAD'] !== $userDob) {
    //     //     return response()->json([
    //     //         'isMember' => false,
    //     //         'error' => 'Date of birth does not match SAN records.'
    //     //     ]);
    //     // }

    //     // return response()->json([
    //     //     'isMember' => true,
    //     //     'message' => 'You are verified as a SAN member.'
    //     // ]);

    //     $doctors = Excel::toCollection(new DoctorsImport, storage_path('app/doctors/Name-list-updated.xlsx'))->first();

    //     $matchByCouncil = $doctors->firstWhere('nmc_no', (int) $userCouncilNum);

    //     if (! $matchByCouncil) {
    //         return response()->json([
    //             'isMember' => false,
    //             'error' => 'Council number not found in membership records.',
    //         ]);
    //     }

    //     // Build full name from Excel for comparison if needed
    //     $fullName = trim(collect([
    //         $matchByCouncil['first_name'],
    //         $matchByCouncil['middle_name'],
    //         $matchByCouncil['last_name'],
    //     ])->filter()->implode(' '));

    //     return response()->json([
    //         'isMember' => true,
    //         'message' => 'You are verified as a SAN member.',
    //         'name' => $fullName,
    //     ]);
    // }

    public function checkCouncilMembership(Request $request)
    {
        $user = auth()->user();
        $userCouncilNum = $user->userDetail->council_number ?? null;
        $userDob = $user->userDetail->dob_ad ?? null;
        $userFirstName = strtolower(trim($user->f_name ?? ''));

        if (! $userCouncilNum) {
            return response()->json([
                'isMember' => false,
                'error' => 'Your council number is missing.',
            ]);
        }

        $memberType = MemberType::find($request->member_type_id)->type ?? null;
        $requestMemberType = strtolower(trim($memberType));
        if (! $memberType) {
            return response()->json([
                'isMember' => false,
                'error' => 'Invalid member type selected.',
            ]);
        }

        $filePath = public_path('doctors/Name-list-updated.xlsx');

        if (! file_exists($filePath)) {
            return response()->json([
                'isMember' => false,
                'error' => 'Membership data file not found.',
            ]);
        }

        $doctors = Excel::toCollection(new DoctorsImport, $filePath)->first();

        $matchByCouncil = $doctors->firstWhere('nmc_no', (int) $userCouncilNum);
        $excelMemberType = strtolower(trim($matchByCouncil['member_type'] ?? ''));
        if (! $matchByCouncil) {
            return response()->json([
                'isMember' => false,
                'error' => 'Council number not found in membership records.',
            ]);
        }

        $excelFirstName = strtolower(trim($matchByCouncil['first_name'] ?? ''));

        if ($excelMemberType !== $requestMemberType) {
            return response()->json([
                'isMember' => false,
                'error' => 'Member type does not match membership records.',
            ]);
        }

        if ($excelFirstName !== $userFirstName) {
            return response()->json([
                'isMember' => false,
                'error' => 'First name does not match membership records.',
            ]);
        }


        $fullName = trim(collect([
            $matchByCouncil['first_name'],
            $matchByCouncil['middle_name'],
            $matchByCouncil['last_name'],
        ])->filter()->implode(' '));

        return response()->json([
            'isMember' => true,
            'message' => 'You are Verified as a ' . $requestMemberType,
            'name' => $fullName,
        ]);
    }
}
