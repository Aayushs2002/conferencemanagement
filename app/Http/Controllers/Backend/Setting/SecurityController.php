<?php

namespace App\Http\Controllers\Backend\Setting;

use App\Http\Controllers\Controller;
use App\Models\Society\SocietySetting;
use App\Models\User\MemberType;
use App\Models\User\Society;
use App\Models\User\UserSociety;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class SecurityController extends Controller
{
    public function index($society = null, $conference = null)
    {
        if (empty($society) && empty($conference)) {
            $layout = 'backend.layouts.main';
        } elseif (!empty($society) && empty($conference)) {
            $layout = 'backend.layouts.society.main';
        } elseif (!empty($society) && !empty($conference)) {
            $layout = 'backend.layouts.conference.main';
        }

        // Get user's member type for the society if society is specified
        $userMemberType = null;
        $memberTypes = [];
        $societyModel = null;
        $hasMemberDetailApi = false;
        if (!empty($society)) {
            // Handle both model instance and ID
            $societyId = is_object($society) ? $society->id : $society;
            $societyModel = is_object($society) ? $society : Society::find($society);
            
            if ($societyModel) {
                // Get society setting to check if member_detail_api exists
                $societySetting = SocietySetting::where('society_id', $societyId)->first();
                $hasMemberDetailApi = !empty($societySetting?->member_detail_api);
                
                // Get user's current member type in this society
                $userSociety = UserSociety::where('user_id', auth()->id())
                ->where('society_id', $societyId)
                ->first();
                
                if ($userSociety) {
                    $userMemberType = MemberType::find($userSociety->member_type_id);
                }
                // dd($userSociety);

                // Get all member types for this society
                $memberTypes = MemberType::where('society_id', $societyId)
                    ->where('status', 1)
                    ->get();
            }
        }

        return view('backend.setting.security.index', compact(
            'layout', 
            'society', 
            'conference', 
            'userMemberType', 
            'memberTypes', 
            'societyModel',
            'hasMemberDetailApi'
        ));
    } 

    public function passwordChange(Request $request)
    {
        $request->validate([
            'currentPassword' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'currentPassword' => 'Your current password is incorrect.',
            ]);
        }
        $user->password = hash_password($request->new_password);
        $user->save();

        return back()->with('status', 'Password changed successfully.');
    }

    public function verifyMemberType(Request $request, $society)
    {
        try {
            // Handle both model instance and ID
            $societyId = is_object($society) ? $society->id : $society;
            
            $user = auth()->user();
            $userCouncilNum = $user->userDetail->council_number ?? null;
            $userDob = $user->userDetail->dob_ad ?? null;

            if (!$userCouncilNum) {
                return response()->json([
                    'success' => false,
                    'error' => 'Your council number is missing. Please update your profile first.'
                ]);
            }

            // Get society setting to fetch member_detail_api
            $societySetting = SocietySetting::where('society_id', $societyId)->first();
            
            if (!$societySetting || !$societySetting->member_detail_api) {
                return response()->json([
                    'success' => false,
                    'error' => 'Member verification is not available for this society. Please contact the administrator.'
                ]);
            }

            // Fetch data from the API
            $apiResponse = Http::timeout(30)->get($societySetting->member_detail_api);

            if (!$apiResponse->successful()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unable to verify membership at this time. Please try again later or contact the administrator.'
                ]);
            }

            $apiData = $apiResponse->json();

            // Find member by council number
            $matchByCouncil = collect($apiData)->firstWhere('councilNum', $userCouncilNum);

            if (!$matchByCouncil) {
                return response()->json([
                    'success' => false,
                    'error' => 'Council number not found in membership records.',
                    'showContact' => true
                ]);
            }

            // Get the member type from API
            $apiMemberType = $matchByCouncil['memberType'] ?? null;

            if (!$apiMemberType) {
                return response()->json([
                    'success' => false,
                    'error' => 'Your member type information is not available in the system.',
                    'showContact' => true
                ]);
            }

            // Check if this member type exists in the society's member types
            $memberType = MemberType::where('society_id', $societyId)
                ->where('type', $apiMemberType)
                ->where('status', 1)
                ->first();

            if (!$memberType) {
                return response()->json([
                    'success' => false,
                    'error' => "Your member type '{$apiMemberType}' could not be matched with the available member types for this society.",
                    'showContact' => true,
                    'apiMemberType' => $apiMemberType
                ]);
            }

            // Optional: Verify DOB if available
            if ($userDob && isset($matchByCouncil['dobAD']) && $matchByCouncil['dobAD'] !== $userDob) {
                return response()->json([
                    'success' => false,
                    'error' => 'Date of birth does not match membership records.',
                    'showContact' => true
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Your membership has been verified successfully.',
                'memberType' => [
                    'id' => $memberType->id,
                    'type' => $memberType->type
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'An error occurred during verification. Please try again or contact the administrator.'
            ], 500);
        }
    }

    public function updateMemberType(Request $request, $society)
    {
        try {
            // Handle both model instance and ID
            $societyId = is_object($society) ? $society->id : $society;
            
            $request->validate([
                'member_type_id' => 'required|exists:member_types,id'
            ]);

            $user = auth()->user();

            // Check if user is already part of this society
            $userSociety = UserSociety::where('user_id', $user->id)
                ->where('society_id', $societyId)
                ->first();

            if (!$userSociety) {
                return response()->json([
                    'success' => false,
                    'error' => 'You are not a member of this society.'
                ], 400);
            }

            // Update the member type
            $userSociety->member_type_id = $request->member_type_id;
            $userSociety->save();

            $memberType = MemberType::find($request->member_type_id);

            return response()->json([
                'success' => true,
                'message' => "Your member type has been updated to '{$memberType->type}' successfully."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while updating member type. Please try again or contact the administrator.'
            ], 500);
        }
    }
}
