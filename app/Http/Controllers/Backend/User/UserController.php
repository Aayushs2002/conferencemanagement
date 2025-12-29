<?php

namespace App\Http\Controllers\Backend\User;

use App\Http\Controllers\Controller;
use App\Models\Committee\CommitteeMember;
use App\Models\Conference\Attendance;
use App\Models\Conference\Author;
use App\Models\Conference\ConferenceRegistration;
use App\Models\Conference\Meal;
use App\Models\Conference\Submission;
use App\Models\Conference\SubmissionDiscussion;
use App\Models\Conference\SubmissionRating;
use App\Models\User;
use App\Models\User\Society;
use App\Models\User\UserDetail;
use App\Models\User\UserInstitution;
use App\Models\Workshop\WorkshopRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    public function index()
    {
        $users = User::where(['type' => 3, 'status' => 1])->get();
        return view('backend.users.user.index', compact('users'));
    }

    public function show(Request $request)
    {
        $user = User::whereId($request->id)->first();
        // dd($user);
        return view('backend.users.user.view', compact('user'));
    }

    public function joinSociety(Request $request)
    {
        $user = User::whereId($request->id)->first();

        $joinedSocietyIds = $user->societies->pluck('id')->toArray();

        $societies = Society::where('status', 1)
            ->whereNotIn('id', $joinedSocietyIds)
            ->get();

        return view('backend.users.user.join-society', compact('societies', 'user'));
    }

    public function joinSocietySubmit(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'society_id' => 'required',
                'member_type_id' => 'required',
            ], [
                'society_id.required' => 'Please select society.',
                'member_type_id.required' => 'Please select Member Type.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::whereId($request->user_id)->first();
            $user->societies()->attach($request->society_id, [
                'member_type_id' => $request->member_type_id,
            ]);


            return response()->json([
                'type' => 'success',
                'message' => 'You have successfully joined the society.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'type' => 'error',
                'message' => 'An unexpected error occurred.',
                'debug' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy(User $user)
    {
        DB::beginTransaction();

        try {
            // 1. Delete submission related records
            $submissionIds = Submission::where('user_id', $user->id)->pluck('id');
            if ($submissionIds->isNotEmpty()) {
                Author::whereIn('submission_id', $submissionIds)->delete();
                SubmissionDiscussion::whereIn('submission_id', $submissionIds)->delete();
                SubmissionRating::whereIn('submission_id', $submissionIds)->delete();
                Submission::whereIn('id', $submissionIds)->delete();
            }

            // 2. Delete conference registration related records
            $registrationIds = ConferenceRegistration::where('user_id', $user->id)->pluck('id');
            if ($registrationIds->isNotEmpty()) {
                Attendance::whereIn('conference_registration_id', $registrationIds)->delete();
                Meal::whereIn('conference_registration_id', $registrationIds)->delete();
                ConferenceRegistration::whereIn('id', $registrationIds)->delete();
            }

            // 3. Delete workshop registrations
            WorkshopRegistration::where('user_id', $user->id)->delete();

            // 4. Delete committee members
            CommitteeMember::where('user_id', $user->id)->delete();

            // 5. Delete experts
            DB::table('experts')->where('user_id', $user->id)->delete();

            // 6. Delete conference user pass designations
            DB::table('conference_user_pass_designations')->where('user_id', $user->id)->delete();

            // 7. Delete activity logs
            DB::table('activity_logs')->where('user_id', $user->id)->delete();

            // 8. Delete login history
            DB::table('login_histories')->where('user_id', $user->id)->delete();

            // 9. Delete workshop ratings
            DB::table('workshop_ratings')->where('user_id', $user->id)->delete();

            // 10. Delete international accommodations
            DB::table('international_accommodations')->where('user_id', $user->id)->delete();

            // 11. Delete conference user permissions
            DB::table('conference_user_permission')->where('user_id', $user->id)->delete();

            // 12. Delete conference user roles
            DB::table('conference_user_roles')->where('user_id', $user->id)->delete();

            // 13. Delete user societies (pivot table)
            $user->societies()->detach();

            // 14. Delete user details
            $user->userDetail()->delete();

            // 15. Delete user institutions
            DB::table('user_institutions')->where('user_id', $user->id)->delete();

            // 16. Delete user designations
            DB::table('user_designations')->where('user_id', $user->id)->delete();

            // 17. Delete user departments
            DB::table('user_departments')->where('user_id', $user->id)->delete();

            // 18. Finally, delete the user
            $user->delete();

            DB::commit();
            return redirect()->back()->with('status', 'User Successfully Deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('delete', 'Error: ' . $e->getMessage());
        }
    }
}
