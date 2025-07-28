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
        // dd($request->all());
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
            $user->userDetail()->delete();
            $user->institution()->delete();
            $user->societies()->detach();

            $submissionIds = Submission::where('user_id', $user->id)->pluck('id');
            Author::whereIn('submission_id', $submissionIds)->delete();
            SubmissionDiscussion::whereIn('submission_id', $submissionIds)->delete();
            SubmissionRating::whereIn('submission_id', $submissionIds)->delete();
            Submission::whereIn('id', $submissionIds)->delete();

            $registrationIds = ConferenceRegistration::where('user_id', $user->id)->pluck('id');
            Attendance::whereIn('conference_id', $registrationIds)->delete();
            Meal::whereIn('conference_id', $registrationIds)->delete();
            ConferenceRegistration::whereIn('id', $registrationIds)->delete();

            CommitteeMember::where('user_id', $user->id)->delete();
            WorkshopRegistration::where('user_id', $user->id)->delete();

            $user->delete();

            DB::commit();
            return redirect()->back()->with('status', 'User Successfully Deleted.');
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return redirect()->back()->with('delete', 'Internal Server Error.');
        }
    }
}
