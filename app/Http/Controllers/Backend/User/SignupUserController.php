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
use App\Models\User\Department;
use App\Models\User\Designation;
use App\Models\User\Institution;
use App\Models\User\MemberType;
use App\Models\User\NamePrefix;
use App\Models\User\Society;
use App\Models\User\UserDetail;
use App\Models\User\UserInstitution;
use App\Models\User\UserDesignation;
use App\Models\User\UserDepartment;
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
                'registrant_type' => 'required|integer|in:1,2,3,4',
                'certificate_required' => 'required|boolean'
            ]);

            $user = User::with('userDetail.namePrefix')->findOrFail($request->user_id);

            // Check if user is already registered for this conference
            $existingRegistration = ConferenceRegistration::where([
                'user_id' => $user->id,
                'conference_id' => $conference->id
            ])->first();

            if ($existingRegistration) {
                throw new \Exception('User is already registered for this conference.');
            }

            DB::beginTransaction();

            // Generate invitation token
            $invitationToken = bin2hex(random_bytes(32));

            // Create registration with invitation token
            $registration = ConferenceRegistration::create([
                'user_id' => $user->id,
                'conference_id' => $conference->id,
                'registrant_type' => $validated['registrant_type'],
                'certificate_required' => $validated['certificate_required'],
                'token' => random_word(60),
                'verified_status' => ConferenceRegistration::STATUS_PENDING,
                'is_invited' => true,
                'attend_type' => ConferenceRegistration::ATTEND_PHYSICAL,
                'total_attendee' => 1,
                'meal_type' => 2,
                'invitation_response_token' => $invitationToken
            ]);

            // Prepare email data
            $middleName = !empty($user->m_name) ? $user->m_name . ' ' : '';
            $data = [
                'namePrefix' => $user->userDetail->namePrefix->prefix ?? '',
                'name' => $user->f_name . ' ' . $middleName . $user->l_name,
                'conference_theme' => $conference->conference_theme,
                'conference_name' => $conference->conference_name,
                'is_invited' => 1,
                'invitationType' => 2,
                'amount' => null,
                'invitation_token' => $invitationToken,
                'invitation_url' => route('invitation.show', $invitationToken)
            ];

            // Send invitation email
            Mail::to($user->email)->send(new RegistrationMail($data, $conference->conference_name));

            // Log activity
            logActivity(
                $conference->id,
                'Invited Conference',
                $user->f_name . ' ' . $middleName . $user->l_name . ' is invited to conference'
            );

            DB::commit();

            $type = 'success';
            $message = "User invited successfully for conference. They will receive an email to accept the invitation.";
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
        // dd($user);
        // $prefixesAll = NamePrefix::whereStatus(1)->get();
        // if ($society && $society->namePrefixes()->exists()) {
        //     $prefixesAll = $society->namePrefixes()->where('status', 1)->get();
        //     // dd($prefixesAll);
        // } else {
        //     // Fallback to all active prefixes if society hasn't selected any
        //     $prefixesAll = NamePrefix::whereStatus(1)->get();
        // }
        $loadData = function ($relation, $model) use ($society) {
            if ($society && $society->$relation()->exists()) {
                return $society->$relation()->where('status', 1)->get();
            }
            return $model::where('status', 1)->get();
        };

        $institutions = $loadData('institutions', Institution::class);
        // dd($institutions);
        $designations = $loadData('designations', Designation::class);
        $departments = $loadData('departments', Department::class); 
        $prefixesAll = $loadData('namePrefixes', NamePrefix::class);

        // Get countries
        $countries = \App\Models\User\Country::where('status', 1)->get();

        // Get user's member type
        $userSociety = $user->societies->first();
        $memberType = $userSociety?->pivot?->memberType;

        // Check for custom institution, designation, department
        $userInstitution = UserInstitution::where('user_id', $user->id)->first();
        $userDesignation = UserDesignation::where('user_id', $user->id)->first();
        $userDepartment = UserDepartment::where('user_id', $user->id)->first();

        return view('backend.users.signup-user.edit-user-profile', compact('user', 'prefixesAll', 'society', 'conference', 'institutions', 'designations', 'departments', 'countries', 'memberType', 'userInstitution', 'userDesignation', 'userDepartment'));
    }

    public function editProfileSubmit(Request $request, $society, $conference)
    {
        try {
            $user = User::whereId($request->user_id)->first();
            
            $rules = [
                'gender' => 'required',
                'f_name' => 'required|string|max:255',
                'm_name' => 'nullable|string|max:255',
                'l_name' => 'required|string|max:255',
                'email' =>  'required|email|unique:users,email,' . $user->id,
                'phone' => 'required|unique:user_details,phone,' . $user->id,
                'institution_id' => 'required',
                'designation_id' => 'required',
                'department_id' => 'required',
                'institute_address' => 'required|string|max:255',
                'country_id' => 'required',
                'council_number' => 'nullable',
                'name_prefix_id' => 'required',
                'member_type_id' => 'required'
            ];

            // Add validation for "other" options
            if ($request->institution_id == 'other') {
                $rules['other_institution_name'] = 'required';
            }
            if ($request->designation_id == 'other') {
                $rules['other_designation'] = 'required';
            }
            if ($request->department_id == 'other') {
                $rules['other_department'] = 'required';
            }

            $validated = $request->validate($rules);

            // Handle "other" options before updating user
            if ($request->institution_id == 'other') {
                unset($validated['institution_id']);
            }
            if ($request->designation_id == 'other') {
                unset($validated['designation_id']);
            }
            if ($request->department_id == 'other') {
                unset($validated['department_id']);
            }

            DB::beginTransaction();

            // Update user basic info
            $user->update([
                'f_name' => $validated['f_name'],
                'm_name' => $validated['m_name'],
                'l_name' => $validated['l_name'],
                'email' => $validated['email'],
            ]);

            // Update user details with null for "other" options
            $user->userDetail->update([
                'gender' => $validated['gender'],
                'phone' => $validated['phone'],
                'name_prefix_id' => $validated['name_prefix_id'],
                'institution_id' => $validated['institution_id'] ?? null,
                'designation_id' => $validated['designation_id'] ?? null,
                'department_id' => $validated['department_id'] ?? null,
                'institute_address' => $validated['institute_address'],
                'country_id' => $validated['country_id'],
                'council_number' => $validated['council_number'],
            ]);

            // Handle custom institution
            if ($request->institution_id == 'other') {
                UserInstitution::where('user_id', $user->id)->delete();
                UserInstitution::create([
                    'user_id' => $user->id,
                    'institution_name' => $request->other_institution_name,
                ]);
            } else {
                // Delete custom institution if switching back to standard option
                UserInstitution::where('user_id', $user->id)->delete();
            }

            // Handle custom designation
            if ($request->designation_id == 'other') {
                UserDesignation::where('user_id', $user->id)->delete();
                UserDesignation::create([
                    'user_id' => $user->id,
                    'designation_name' => $request->other_designation,
                ]);
            } else {
                // Delete custom designation if switching back to standard option
                UserDesignation::where('user_id', $user->id)->delete();
            }

            // Handle custom department
            if ($request->department_id == 'other') {
                UserDepartment::where('user_id', $user->id)->delete();
                UserDepartment::create([
                    'user_id' => $user->id,
                    'department_name' => $request->other_department,
                ]);
            } else {
                // Delete custom department if switching back to standard option
                UserDepartment::where('user_id', $user->id)->delete();
            }

            // Update society membership
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
                'pass_designation' => 'required',
                'color' => 'required'
            ]);
            $user = User::whereId($request->user_id)->first();
            ConferenceUserPassDesignation::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'conference_id' => $conference->id,
                ],
                [
                    'pass_designation' => $request->pass_designation,
                    'color' => $request->color
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

            // Prevent merging a user with itself
            if ($mainUser->id === $secondUser->id) {
                throw new \Exception('Cannot merge a user with itself.');
            }

            DB::beginTransaction();

            // 1. Merge Conference Registrations (avoid duplicates)
            DB::table('conference_registrations')
                ->where('user_id', $secondUser->id)
                ->whereNotIn('conference_id', function($query) use ($mainUser) {
                    $query->select('conference_id')
                        ->from('conference_registrations')
                        ->where('user_id', $mainUser->id);
                })
                ->update(['user_id' => $mainUser->id]);
            
            DB::table('conference_registrations')->where('user_id', $secondUser->id)->delete();

            // 2. Merge Submissions
            DB::table('submissions')
                ->where('user_id', $secondUser->id)
                ->update(['user_id' => $mainUser->id]);

            // 3. Merge Workshop Registrations (avoid duplicates)
            DB::table('workshop_registrations')
                ->where('user_id', $secondUser->id)
                ->whereNotIn('workshop_id', function($query) use ($mainUser) {
                    $query->select('workshop_id')
                        ->from('workshop_registrations')
                        ->where('user_id', $mainUser->id);
                })
                ->update(['user_id' => $mainUser->id]);
            
            DB::table('workshop_registrations')->where('user_id', $secondUser->id)->delete();

            // 4. Merge Experts (avoid duplicates)
            DB::table('experts')
                ->where('user_id', $secondUser->id)
                ->whereNotIn('conference_id', function($query) use ($mainUser) {
                    $query->select('conference_id')
                        ->from('experts')
                        ->where('user_id', $mainUser->id);
                })
                ->update(['user_id' => $mainUser->id]);
            
            DB::table('experts')->where('user_id', $secondUser->id)->delete();

            // 5. Merge Conference User Pass Designations (avoid duplicates)
            DB::table('conference_user_pass_designations')
                ->where('user_id', $secondUser->id)
                ->whereNotIn('conference_id', function($query) use ($mainUser) {
                    $query->select('conference_id')
                        ->from('conference_user_pass_designations')
                        ->where('user_id', $mainUser->id);
                })
                ->update(['user_id' => $mainUser->id]);
            
            DB::table('conference_user_pass_designations')->where('user_id', $secondUser->id)->delete();

            // 6. Merge Activity Logs
            DB::table('activity_logs')
                ->where('user_id', $secondUser->id)
                ->update(['user_id' => $mainUser->id]);

            // 7. Merge Login History
            DB::table('login_histories')
                ->where('user_id', $secondUser->id)
                ->update(['user_id' => $mainUser->id]);

            // 8. Merge Workshop Ratings (avoid duplicates)
            DB::table('workshop_ratings')
                ->where('user_id', $secondUser->id)
                ->whereNotIn('workshop_id', function($query) use ($mainUser) {
                    $query->select('workshop_id')
                        ->from('workshop_ratings')
                        ->where('user_id', $mainUser->id);
                })
                ->update(['user_id' => $mainUser->id]);
            
            DB::table('workshop_ratings')->where('user_id', $secondUser->id)->delete();

            // 9. Merge International Accommodations
            DB::table('international_accommodations')
                ->where('user_id', $secondUser->id)
                ->update(['user_id' => $mainUser->id]);

            // 10. Merge Conference User Permissions (avoid duplicates)
            DB::table('conference_user_permission')
                ->where('user_id', $secondUser->id)
                ->whereNotExists(function($query) use ($mainUser) {
                    $query->select(DB::raw(1))
                        ->from('conference_user_permission as cup2')
                        ->whereColumn('cup2.conference_id', 'conference_user_permission.conference_id')
                        ->whereColumn('cup2.permission_id', 'conference_user_permission.permission_id')
                        ->where('cup2.user_id', $mainUser->id);
                })
                ->update(['user_id' => $mainUser->id]);
            
            DB::table('conference_user_permission')->where('user_id', $secondUser->id)->delete();

            // 11. Merge Conference User Roles (avoid duplicates)
            DB::table('conference_user_roles')
                ->where('user_id', $secondUser->id)
                ->whereNotExists(function($query) use ($mainUser) {
                    $query->select(DB::raw(1))
                        ->from('conference_user_roles as cur2')
                        ->whereColumn('cur2.conference_id', 'conference_user_roles.conference_id')
                        ->whereColumn('cur2.role_id', 'conference_user_roles.role_id')
                        ->where('cur2.user_id', $mainUser->id);
                })
                ->update(['user_id' => $mainUser->id]);
            
            DB::table('conference_user_roles')->where('user_id', $secondUser->id)->delete();

            // 12. Merge User Societies (avoid duplicates)
            DB::table('user_societies')
                ->where('user_id', $secondUser->id)
                ->whereNotIn('society_id', function($query) use ($mainUser) {
                    $query->select('society_id')
                        ->from('user_societies')
                        ->where('user_id', $mainUser->id);
                })
                ->update(['user_id' => $mainUser->id]);
            
            DB::table('user_societies')->where('user_id', $secondUser->id)->delete();

            // 13. Merge Committee Members
            DB::table('committee_members')
                ->where('user_id', $secondUser->id)
                ->update(['user_id' => $mainUser->id]);

            // 14. Delete User Details of second user
            DB::table('user_details')->where('user_id', $secondUser->id)->delete();

            // 15. Delete User Institutions
            DB::table('user_institutions')->where('user_id', $secondUser->id)->delete();

            // 16. Delete User Designations
            DB::table('user_designations')->where('user_id', $secondUser->id)->delete();

            // 17. Delete User Departments
            DB::table('user_departments')->where('user_id', $secondUser->id)->delete();

            // 18. Finally, delete the second user
            $secondUser->delete();

            DB::commit();

            // Log the merge activity
            logActivity(
                $conference->id,
                'User Merged',
                'User ' . $secondUser->email . ' (ID: ' . $secondUser->id . ') merged into ' . $mainUser->email . ' (ID: ' . $mainUser->id . ')'
            );

            $message = 'User merged successfully. All data has been transferred and the duplicate user has been deleted.';
            $type = 'success';
        } catch (\Exception $e) {
            DB::rollBack();
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

            Mail::to($user->email)->send(new ResetPasswordMail($data, $conference->conference_name));

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
            Mail::to($user->email)->send(new UserCreatedMail($user->email, $password, $conference->conference_name));
            DB::commit();

            return response()->json(['type' => 'success', 'message' => 'User added successfully.']);
        } catch (Exception $e) {
            // dd($e);
            DB::rollBack();
            return response()->json(['type' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
