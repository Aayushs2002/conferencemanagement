<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Models\User\UserInstitution;
use App\Services\File\FileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(protected FileService $file_service) {}

    public function updateProfile(Request $request)
    {
        try {
            $rules = [
                'institution_id' => 'required',
                'designation_id' => 'required',
                'department_id' => 'required',
                'institute_address' => 'required',
                'image' => 'required',
                'other_institution_name' => 'required',
            ];

            $messages = [
                'institution_id.required' => 'Please select institution Name.',
                'designation_id.required' => 'Please select Designation.',
                'department_id.required' => 'Please select Department.',
            ];

            $namePrefixId = current_user()->userDetail->name_prefix_id;
            if (($namePrefixId == 1 || $namePrefixId == 3) && current_user()->userDetail->country_id == 125) {
                $rules['council_number'] = 'required';
                $messages['council_number.required'] = 'The council number is required.';
            } else {
                $rules['council_number'] = 'nullable';
            }

            if ($request->institution_id == 'other') {
                $rules['other_institution_name'] = 'required';
            }

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }
            $data = $validator->validate();
            $user = current_user();
            $userData = User::whereId($user->id)->first();
            if (!empty($data['image'])) {
                $this->file_service->deleteFile($user->userDetail->image, 'profile/image');
                $data['image'] = $this->file_service->fileUpload($data['image'], 'profile', 'profile/image');
            }
            DB::beginTransaction();
            if ($request->institution_id == 'other') {
                unset($data['institution_id']);
            }

            // dd($data);
            $userData->userDetail->update($data);
            $userData->update([
                'is_profile_updated' => 1
            ]);

            if ($request->institution_id == 'other') {
                UserInstitution::create([
                    'user_id' => $userData->id,
                    'institution_name' => $request->other_institution_name
                ]);
            }
            DB::commit();

            session()->forget('show_profile_update_modal');
            return response()->json([
                'type' => 'success',
                'message' => 'You have successfully updated profile.'
            ]);
        } catch (\Throwable $th) {
            dd($th);
            DB::rollBack();
            return response()->json([
                'type' => 'error',
                'message' => 'An unexpected error occurred.',
                'debug' => $th->getMessage()
            ], 500);
        }
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
