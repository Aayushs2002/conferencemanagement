<?php

namespace App\Http\Controllers\Backend\User;

use App\Http\Controllers\Controller;
use App\Mail\Conference\RegistrationMail;
use App\Mail\User\ResetPasswordMail;
use App\Mail\User\UserCreatedMail;
use App\Models\Conference\Conference;
use App\Models\Conference\ConferenceRegistration;
use App\Models\Conference\Expert;
use App\Models\Conference\Submission;
use App\Models\LoginHistory;
use App\Models\User;
use App\Models\User\ConferenceUserPassDesignation;
use App\Models\User\MemberType;
use App\Models\User\NamePrefix;
use App\Models\User\Society;
use App\Models\User\UserDetail;
use App\Models\Workshop\Workshop;
use App\Models\Workshop\WorkshopRegistration;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SignupUserController extends Controller
{
    public function index($society, $conference)
    {
        $conferenceId = $conference->id;

        $society = Society::with(['users' => function ($query) use ($conferenceId) {
            $query->where('type', 3)
                ->orderByDesc('id')
                ->with([
                    'conferenceRegistration' => function ($q) use ($conferenceId) {
                        $q->where('conference_id', $conferenceId);
                    },
                    'workshopRegistration',
                    'submission' => function ($q) use ($conferenceId) {
                        $q->where('conference_id', $conferenceId);
                    },
                ]);
        }])->where([
            'id' => $conference->society_id,
            'status' => 1
        ])->first();

        $users = $society ? $society->users : collect();
        // dd($users);
        return view('backend.users.signup-user.index', compact('users', 'society', 'conference'));
    }


    public function makeExpert(Request $request, $society, $conference)
    {
        try {
            $type = 'success';

            $isExpert = Expert::where(['user_id' => $request->userId, 'conference_id' => $conference->id])->first();

            if (empty($isExpert)) {
                $data['user_id'] = $request->userId;
                $data['conference_id'] = $conference->id;
                $data['status'] = 1;
                Expert::create($data);
                $message = 'User Assigned as Expert Successfully for ' . $conference->conference_theme;
            } else {
                if ($isExpert->status == 1) {
                    $isExpert->update(['status' => 0]);
                    $message = 'User Removed as Expert Successfully for ' . $conference->conference_theme;
                } else {
                    $isExpert->update(['status' => 1]);
                    $message = 'User Assigned as Expert Successfully for ' . $conference->conference_theme;
                }
            }
        } catch (Exception $e) {
            $type = 'error';
            $message = $e->getMessage();
        }
        return response()->json(['type' => $type, 'message' => $message]);
    }

    public function inviteForConference(Request $request, $society, $conference)
    {
        $user = User::whereId($request->id)->first();
        return view('backend.users.signup-user.invite-for-conference', compact('user', 'society', 'conference'));
    }

    public function inviteForConferenceSubmit(Request $request, $society, $conference)
    {
        try {
            $validated = $request->validate([
                'registrant_type' => 'required',
                'certificate_required' => 'required'
            ]);
            $validated['user_id'] = $request->user_id;
            $validated['conference_id'] = $conference->id;
            $validated['token'] = random_word(60);
            $validated['verified_status'] = 1;
            $validated['is_invited'] = 1;
            $validated['attend_type'] = 1;
            $validated['total_attendee'] = 1;
            $validated['meal_type'] = 2;

            $user = User::whereId($validated['user_id'])->first();
            $middleName = !empty($user->m_name) ? $user->m_name . ' ' : '';
            $data = [
                'namePrefix' => $user->userDetail->namePrefix->prefix,
                'name' => $user->f_name . ' ' . $middleName . $user->l_name,
                'conference_theme' => $conference->conference_theme,
                'conference_name' => $conference->conference_name,
                'invitationType' => 2,
                'amount' => null
            ];
            DB::beginTransaction();
            Mail::to($user->email)->send(new RegistrationMail($data));
            ConferenceRegistration::create($validated);
            logActivity($conference->id, 'Invited Conference',  $user->f_name . ' ' . $middleName . $user->l_name . ' is registered to conference');
            DB::commit();
            $type = 'success';
            $message = "User invited successfully for conference.";
        } catch (Exception $e) {
            DB::rollBack();
            $type = 'error';
            $message = $e->getMessage();
        }
        return response()->json(['type' => $type, 'message' => $message]);
    }

    public function show(Request $request, $society, $conference)
    {
        $user = User::whereId($request->id)->first();
        return view('backend.users.signup-user.view', compact('user', 'society', 'conference'));
    }
    public function editProfile(Request $request, $society, $conference)
    {
        $user = User::whereId($request->id)->first();
        $prefixesAll = NamePrefix::whereStatus(1)->get();
        return view('backend.users.signup-user.edit-user-profile', compact('user', 'prefixesAll', 'society', 'conference'));
    }

    public function editProfileSubmit(Request $request, $society, $conference)
    {
        try {
            $user = User::whereId($request->user_id)->first();
            $validated = $request->validate([
                'gender' => 'required',
                'f_name' => 'required|string|max:255',
                'm_name' => 'nullable|string|max:255',
                'l_name' => 'required|string|max:255',
                'email' =>  'required|email|unique:users,email,' . $user->id,
                'unique:user_details,phone,' . $user->id . ',user_id',
                'institution_id' => 'required',
                'designation_id' => 'required',
                'department_id' => 'required',
                'institute_address' => 'required|string:255',
                'country_id' => 'required',
                'council_number' => 'required',
                'name_prefix_id' => 'required',
                'member_type_id' => 'required'
            ]);
            DB::beginTransaction();

            $user->update($validated);
            $user->userDetail->update($validated);

            $user->societies()->updateExistingPivot($society->id, [
                'member_type_id' => $validated['member_type_id'],
            ]);

            DB::commit();
            $type = 'success';
            $message = "User Profile Edit successfully.";
        } catch (Exception $e) {
            DB::rollBack();
            $type = 'error';
            $message = $e->getMessage();
        }
        return response()->json(['type' => $type, 'message' => $message]);
    }

    public function passDesgination(Request $request, $society, $conference)
    {
        $user = User::whereId($request->id)->first();
        $passDesignation = ConferenceUserPassDesignation::where(['user_id' => $user->id, 'conference_id' => $conference->id])->first();
        return view('backend.users.signup-user.pass-designation', compact('user', 'passDesignation', 'society', 'conference'));
    }
    public function passDesginationSubmit(Request $request, $society, $conference)
    {
        try {
            $validated = $request->validate([
                'pass_designation' => 'required'
            ]);
            $user = User::whereId($request->user_id)->first();
            ConferenceUserPassDesignation::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'conference_id' => $conference->id,
                ],
                [
                    'pass_designation' => $request->pass_designation
                ]
            );
            $message = 'Designation Passed Successfully Added';
            $type = 'success';
        } catch (\Exception $e) {
            $type = 'error';
            $message = $e->getMessage();
        }
        return response()->json(['type' => $type, 'message' => $message]);
    }

    public function mergeUser(Request $request, $society, $conference)
    {
        $user = User::whereId($request->id)->first();
        $society = Society::with(['users' => function ($query) {
            $query->where('type', 3)->orderByDesc('id');
        }])->where([
            'id' => $conference->society_id,
            'status' => 1
        ])->first();

        $users = $society ? $society->users : collect();
        return view('backend.users.signup-user.merge-user', compact('society', 'conference', 'user', 'users'));
    }

    public function mergeUserSubmit(Request $request, $society, $conference)
    {
        try {
            $validated = $request->validate([
                'second_user_id' => 'required|exists:users,id'
            ]);

            $mainUser = User::findOrFail($request->user_id);
            $secondUser = User::findOrFail($request->second_user_id);

            $conferenceId = $conference->id;
            ConferenceRegistration::where('user_id', $secondUser->id)
                ->where('conference_id', $conferenceId)
                ->update(['user_id' => $mainUser->id]);


            Submission::where('user_id', $secondUser->id)
                ->where('conference_id', $conferenceId)
                ->update(['user_id' => $mainUser->id]);


            $conferenceWorkshopIds = Workshop::where('conference_id', $conferenceId)
                ->pluck('id')
                ->toArray();

            $secondUserWorkshops = WorkshopRegistration::where('user_id', $secondUser->id)
                ->whereIn('workshop_id', $conferenceWorkshopIds)
                ->get();

            foreach ($secondUserWorkshops as $registration) {
                $alreadyRegistered = WorkshopRegistration::where('user_id', $mainUser->id)
                    ->where('workshop_id', $registration->workshop_id)
                    ->exists();

                if (!$alreadyRegistered) {
                    $registration->user_id = $mainUser->id;
                    $registration->save();
                } else {
                    $registration->delete();
                }
            }

            $message = 'User Merged Successfully';
            $type = 'success';
        } catch (\Exception $e) {
            $type = 'error';
            $message = $e->getMessage();
        }
        return response()->json(['type' => $type, 'message' => $message]);
    }

    public function resetPassword($society, $conference, Request $request)
    {
        try {
            $type = 'success';
            $message = 'Password has been reset successfully.';
            $user = User::whereId($request->userId)->first();

            $generatedPassword = random_word(8);
            $hashedPassword = hash_password($generatedPassword);

            $data = [
                'receiverName' => $user->fullName($user),
                'loginEmail' => $user->email,
                'generatedPassword' => $generatedPassword,
                'conference_name' => $conference->conference_name
            ];

            Mail::to($user->email)->send(new ResetPasswordMail($data));

            $user->update(['password' => $hashedPassword]);
        } catch (Exception $e) {
            $type = 'error';
            $message = $e->getMessage();
        }

        return response(['type' => $type, 'message' => $message]);
    }

    public function loginHistory($society, $conference, Request $request)
    {
        $user = User::where('id', $request->id)->first();
        $histories = LoginHistory::where('user_id', $user->id)->get();
        return view('backend.users.signup-user.login-history', compact('histories', 'user'));
    }

    public function addUserForm($society, $conference)
    {
        $memberTypes = MemberType::where(['society_id' => $society->id, 'status' => 1])->get();
        return view('backend.users.signup-user.add-user', compact('society', 'conference'));
    }


    public function addUserSubmit(Request $request, $society, $conference)
    {
        $validated = $request->validate([
            'gender' => 'required',
            'f_name' => 'required|string|max:255',
            'm_name' => 'nullable|string|max:255',
            'l_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|unique:user_details,phone',
            'country_id' => 'required',
            'name_prefix_id' => 'required',
            'member_type_id' => 'required'
        ]);
        try {

            DB::beginTransaction();

            // Generate password
            $password = random_word(8);

            // Create User
            $user = User::create([
                'f_name' => $validated['f_name'],
                'm_name' => $validated['m_name'] ?? null,
                'l_name' => $validated['l_name'],
                'email' => $validated['email'],
                'password' => hash_password($password),
                'type' => 3,

            ]);

            // Create User Detail
            UserDetail::create([
                'user_id' => $user->id,
                'phone' => $validated['phone'],
                'country_id' => $validated['country_id'],
                'name_prefix_id' => $validated['name_prefix_id'],
                'gender' => $validated['gender'],

            ]);

            $user->societies()->attach($society->id, [
                'member_type_id' => $validated['member_type_id'],
            ]);
            Mail::to($user->email)->send(new UserCreatedMail($user->email, $password));
            DB::commit();

            return response()->json(['type' => 'success', 'message' => 'User added successfully.']);
        } catch (Exception $e) {
            // dd($e);
            DB::rollBack();
            return response()->json(['type' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
