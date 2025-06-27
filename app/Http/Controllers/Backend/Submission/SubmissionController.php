<?php

namespace App\Http\Controllers\Backend\Submission;

use App\Http\Controllers\Controller;
use App\Jobs\SendSubmissionBulkMailJob;
use App\Models\Conference\Expert;
use App\Models\Conference\Submission;
use App\Models\Conference\SubmissionCategoryMajorTrack;
use App\Models\Conference\SubmissionDiscussion;
use App\Models\SubmissionSetting;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubmissionController extends Controller
{
    public function index(Request $request, $society, $conference)
    {
        // $conferenceDetail = conference_detail();

        // if (empty($conferenceDetail)) {
        //     return redirect()->route('dashboard');
        // }
        $submissionTracks = SubmissionCategoryMajorTrack::where(['conference_id' => $conference->id, 'status' => 1])->get();
        $query = Submission::with('discussions')->where(['conference_id' => $conference->id, 'status' => 1]);
        if ($request->filled('article_type')) {
            $query->where('article_type', $request->article_type);
        }

        if ($request->filled('presentation_type')) {
            $query->where('presentation_type', $request->presentation_type);
        }

        if ($request->filled('request_status')) {
            $query->where('request_status', $request->request_status);
        }
        if ($request->filled('submission_category_major_track_id')) {
            $query->where('submission_category_major_track_id', $request->submission_category_major_track_id);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $submissions = $query->latest()->get();
        return view('backend.submission.submission.index', compact('submissions', 'submissionTracks', 'conference', 'society'));
    }

    public function show(Request $request)
    {
        $submission = Submission::whereId($request->id)->first();

        return view('backend.submission.submission.view', compact('submission'));
    }

    public function expertForwardForm(Request $request, $society, $conference)
    {



        $setting = SubmissionSetting::where('conference_id', $conference->id)->select('abstract_word_limit', 'key_word_limit')->first();

        $submission = Submission::whereId($request->id)->first();
        $experts = Expert::where(['conference_id' => $submission->conference_id, 'status' => 1])->whereNot('user_id', $submission->user_id)->get();
        return view('backend.submission.submission.expert-forward-modal', compact('submission', 'experts', 'setting', 'society', 'conference'));
    }

    // forward presentation request to expert
    public function expertForward(Request $request)
    {
        try {
            $type = 'success';
            $message = 'Forwarded to expert successfully.';
            $validated = $request->validate([
                'expert_id' => 'required',
                'abstract_content' => 'required'
            ]);
            if ($validated) {
                $submission = Submission::whereId($request->id)->first();
                if ($validated['expert_id'] == $submission->user_id) {
                    throw new Exception("Presenter and Expert should not be same.", 1);
                }
                $validated['forward_expert'] = 1;

                // $expert = User::whereId($validated['expert_id'])->first();



                // $mailData = [
                //     'name' => $expert->fullName($expert),
                //     'namePrefix' => $expert->namePrefix->prefix,
                //     'topic' => $submission->topic
                // ];

                // Mail::to($expert->email)->send(new ExpertForwardMail($mailData, $cc));
                $validated['review_status'] = 0;
                $submission->update($validated);
            }
        } catch (Exception $e) {
            $type = 'error';
            $message = $e->getMessage();
        }
        return response()->json(['type' => $type, 'message' => $message]);
    }


    public function sentToAuthorForm(Request $request, $society, $conference)
    {
        $submission = Submission::whereId($request->id)->first();

        $discussions = SubmissionDiscussion::where('submission_id', $submission->id)->get();

        return view('backend.submission.submission.sent-to-author-modal', compact('submission', 'discussions', 'society', 'conference'));
    }

    public function sentToAuthor(Request $request)
    {
        try {
            $submission = Submission::whereId($request->id)->first();
            // dd($submission);


            // if ($request->request_status != 3) {
            $rules['remarks'] = 'required';
            // }
            $rules['request_status'] = 'required';

            $validated = $request->validate($rules);
            // $validated['presenter_name'] = $submission->presenter->fullName($submission, 'presenter');
            // $validated['namePrefix'] = $submission->presenter->namePrefix->prefix;
            // $validated['topic'] = $submission->topic;
            // $validated['presentation_type'] = $submission->presentation_type;
            if ($request->request_status == 1) {
                $message = 'Request accepted successfully.';
                // Mail::to($submission->presenter->email)->send(new SubmissionAcceptMail($validated));
            }
            if ($request->request_status == 2) {
                $message = 'Request updated for correction..';
                // Mail::to($submission->presenter->email)->send(new SubmissionCorrectionMail($validated));
            }
            if ($request->request_status == 3) {
                $message = 'Request rejected successfully.';
                // Mail::to($submission->presenter->email)->send(new SubmissionRejectMail($validated));
            }
            DB::beginTransaction();

            $submission->update(['request_status' => $validated['request_status']]);


            // insert into table 2
            $validated['submission_id'] = $request->id;
            $validated['sender_id'] = current_user()->id;
            SubmissionDiscussion::create($validated);

            DB::commit();
            return response()->json(['message' => $message]);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function viewDiscussion($society, $conference, $submission)
    {
        $discussions = SubmissionDiscussion::where(['submission_id' => $submission->id, 'status' => 1])->get();
        return view('backend.submission.discussion.index', compact('discussions', 'submission'));
    }

    public function convertPresentationTypeRequest($id)
    {
        $submission = Submission::whereId($id)->first();


        // $mailData['presenter_name'] = $submission->presenter->fullName($submission, 'presenter');
        // $mailData['topic'] = $submission->topic;
        // $mailData['presentation_type'] = $submission->presentation_type;
        // $mailData['namePrefix'] = $submission->presenter->namePrefix->prefix;
        // dd($mailData);   

        // Mail::to($submission->presenter->email)->send(new ConvertPresentationTypeMail($mailData));
        $submission->update(['presentation_type_change' => 0]);
        // $submission->update(['presentation_type' => $newValue]);
        return redirect()->back()->with('status', 'Presentation type changed successfully.');
    }

    public function viewScore(Request $request)
    {
        $submission = Submission::whereId($request->id)->first();

        return view('backend.submission.submission.view-score', compact('submission'));
    }

    public function sendMail($society, $conference)
    {
        return view('backend.submission.submission.send-mail', compact('society', 'conference'));
    }

    public function sendMailSubmit(Request $request)
    {
        try {
            $type = 'success';
            $message = 'Mail Send Succssfully.';

            $validated = $request->validate([
                'user_type' => 'required',
                'presentation_type' => 'required',
                'User' => 'required',
                'subject' => 'required',
                'mail_content' => 'required',
            ]);

            $users = json_decode($validated['User']);

            foreach ($users as $user) {
                SendSubmissionBulkMailJob::dispatch($user, $validated['subject'], $validated['mail_content']);
            }
        } catch (Exception $e) {
            $type = 'error';
            $message = $e->getMessage();
        }
        return response()->json(['type' => $type, 'message' => $message]);
    }

    public function getUsersByTypeAndPresentation(Request $request, $society, $conference)
    {
        $userType = $request->input('user_type');
        $presentationType = $request->input('presentation_type');

        if (!$userType || !$presentationType) {
            return response()->json([]);
        }

        $types = ($presentationType == 3) ? [1, 2] : [$presentationType];

        if ($userType == 1) {
            $submissions = Submission::whereIn('presentation_type', $types)
                ->where('conference_id', $conference->id)
                ->with('presenter:id,f_name,m_name,l_name,email')
                ->get()
                ->pluck('presenter')
                ->unique('id');
        } elseif ($userType == 2) {
            $submissions = Submission::whereIn('presentation_type', $types)
                ->where('conference_id', $conference->id)
                ->with('expert:id,f_name,m_name,l_name,email')
                ->get()
                ->pluck('expert')
                ->unique('id');
        } else {
            return response()->json([]);
        }

        $users = $submissions->filter()
            ->map(function ($user) {
                return [
                    'value' => $user->id,
                    'name' => trim("{$user->f_name} {$user->m_name} {$user->l_name}"),
                    'email' => $user->email,
                    'avatar' => 'https://i.pravatar.cc/80?u=' . urlencode($user->email),
                ];
            })->values();

        return response()->json($users);
    }
}
