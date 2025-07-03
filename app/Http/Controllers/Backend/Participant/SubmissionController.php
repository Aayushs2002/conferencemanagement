<?php

namespace App\Http\Controllers\Backend\Participant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Participant\SubmissionRequest;
use App\Mail\Submission\SubmissionSubmittedToUserMail;
use App\Models\Conference\Author;
use App\Models\Conference\Submission;
use App\Models\Conference\SubmissionCategoryMajorTrack;
use App\Models\Conference\SubmissionDiscussion;
use App\Models\SubmissionSetting;
use App\Models\User;
use App\Services\File\FileService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

class SubmissionController extends Controller
{
    public function __construct(protected FileService $file_service) {}

    public function index($society, $conference)
    {
        $submissions = Submission::with('discussions')
            ->where(function ($query) use ($conference) {
                $query->where('conference_id', $conference->id)
                    ->where('user_id', current_user()->id)
                    ->where('status', 1);
            })
            ->orWhere(function ($query) use ($conference) {
                $query->where('conference_id', $conference->id)
                    ->where('expert_id', current_user()->id);
            })
            ->get();
            // dd($submissions,current_user());    
        $submissionSetting = SubmissionSetting::where('conference_id', $conference->id)->first();
        // dd($submissionSetting);
        return view('backend.participant.submission.index', compact('conference', 'submissions', 'society', 'submissionSetting'));
    }

    public function create($society, $conference)
    {

        $setting = SubmissionSetting::where('conference_id', $conference->id)
            ->select('abstract_word_limit', 'key_word_limit', 'deadline')
            ->first();
        if (!$setting) {
            return redirect()->back()->with('delete', 'Submission settings not found.');
        }
        if (is_past($setting->deadline)) {
            return redirect()->back()->with('delete', 'Submission date has ended.');
        }
        $submissionTracks = SubmissionCategoryMajorTrack::where(['conference_id' => $conference->id, 'status' => 1])->get();

        return view('backend.participant.submission.create', compact('society', 'conference', 'submissionTracks', 'setting'));
    }

    public function store(SubmissionRequest $request, $society, $conference)
    {
        try {
            $validated = $request->all();
            // dd($validated);
            $setting = SubmissionSetting::where('conference_id', $conference->id)->select('abstract_word_limit', 'key_word_limit')->first();
            // dd('ad');
            if (!empty($validated['keywords']) && !empty($setting->key_word_limit)) {
                $keywordsCount = count(explode(',', $request->keywords));
                // dd($validated['keywords']);
                if ($keywordsCount > $setting->key_word_limit) {
                    return redirect()->back()->withInput()->with('delete', 'Keywords word limit exceeded.');
                }
                $keywordArray = json_decode($request->keywords, true);
                $validated['keywords']  = is_array($keywordArray)
                    ? implode(',', array_column($keywordArray, 'value'))
                    : '';
            }

            $abstractWordCount = str_word_count(strip_tags($request->abstract_content));
            if (!empty($setting->abstract_word_limit) && $abstractWordCount > $setting->abstract_word_limit) {
                return redirect()->back()->withInput()->with('delete', 'Abstract word limit exceeded.');
            }

            if (!empty($validated['image'])) {
                $validated['image'] = $this->file_service->fileUpload($validated['image'], 'diagram', 'participant/submission/image');
            }
            $authUser = User::whereId(current_user())->first();
            $validated['user_id'] = current_user()->id;
            $validated['conference_id'] = $conference->id;
            $validated['submitted_date'] = now();

            $start = \Carbon\Carbon::parse($conference->start_date);
            $end = \Carbon\Carbon::parse($conference->end_date);

            if ($start->month === $end->month && $start->year === $end->year) {
                // Same month and year: 10–12 April 2025
                $conferenceDate = $start->format('d') . '-' . $end->format('d F Y');
            } elseif ($start->year === $end->year) {
                // Same year but different months: 28 March – 2 April 2025
                $conferenceDate = $start->format('d F') . ' - ' . $end->format('d F Y');
            } else {
                // Different years: 30 December 2024 – 2 January 2025
                $conferenceDate = $start->format('d F Y') . ' - ' . $end->format('d F Y');
            }
            $userMailData = [
                'name' => $authUser->fullName($authUser),
                'namePrefix' => $authUser->userDetail->namePrefix->prefix,
                'topic' => $validated['title'],
                'conferenceTheme' => $conference->conference_theme,
                'societyEmail' => $society->contact_person_email,
                'societyName' => $society->abbreviation,
                'conferenceDate' => $conferenceDate
            ];
            Mail::to($authUser->email)->send(new SubmissionSubmittedToUserMail($userMailData));
            DB::beginTransaction();
            // dd(current_user()->userDetail->phone);
            $submission = Submission::create($validated);
            $validated['submission_id'] = $submission->id;
            $validated['name'] = current_user()->fullName(current_user());
            $validated['email'] = current_user()->email;
            $validated['phone'] = current_user()->userDetail->phone;
            $validated['designation'] = current_user()->userDetail->designation->designation;
            $validated['institution'] = current_user()->userDetail->institution->name;
            $validated['institution_address'] = current_user()->userDetail->institute_address;

            Author::create($validated);
            DB::commit();
            return redirect()->route('my-society.conference.submission.index',  [$society, $conference])->with('status', 'Submission Added Successfully');
        } catch (\Exception $th) {
            DB::rollBack();

            dd($th);
        }
    }


    public function view(Request $request, $society, $conference)
    {
        // dd($request->all());
        $submission = Submission::whereId($request->id)->first();
        return view('backend.participant.submission.view', compact('submission'));
    }

    public function edit($society, $conference, $submission)
    {
        $setting = SubmissionSetting::where('conference_id', $conference->id)
            ->select('abstract_word_limit', 'key_word_limit', 'deadline')
            ->first();
        if (!$setting) {
            return redirect()->back()->with('delete', 'Submission settings not found.');
        }
        if (is_past($setting->deadline)) {
            return redirect()->back()->with('delete', 'Submission date has ended.');
        }
        $submissionTracks = SubmissionCategoryMajorTrack::where(['conference_id' => $conference->id, 'status' => 1])->get();

        return view('backend.participant.submission.create', compact('society', 'conference', 'submissionTracks', 'setting', 'submission'));
    }

    public function update(SubmissionRequest $request, $society, $conference, $submission)
    {
        try {
            $validated = $request->all();
            // dd($validated);
            $setting = SubmissionSetting::where('conference_id', $conference->id)->select('abstract_word_limit', 'key_word_limit')->first();
            // dd('ad');
            if (!empty($validated['keywords']) && !empty($setting->key_word_limit)) {
                $keywordsCount = count(explode(',', $request->keywords));
                // dd($validated['keywords']);
                if ($keywordsCount > $setting->key_word_limit) {
                    return redirect()->back()->withInput()->with('delete', 'Keywords word limit exceeded.');
                }
                $keywordArray = json_decode($request->keywords, true);
                $validated['keywords']  = is_array($keywordArray)
                    ? implode(',', array_column($keywordArray, 'value'))
                    : '';
            }

            $abstractWordCount = str_word_count(strip_tags($request->abstract_content));
            if (!empty($setting->abstract_word_limit) && $abstractWordCount > $setting->abstract_word_limit) {
                return redirect()->back()->withInput()->with('delete', 'Abstract word limit exceeded.');
            }

            if (!empty($validated['image'])) {
                $this->file_service->deleteFile($submission->image, 'participant/submission/image');
                $validated['image'] = $this->file_service->fileUpload($validated['image'], 'diagram', 'participant/submission/image');
            }

            $validated['user_id'] = current_user()->id;
            $validated['conference_id'] = $conference->id;
            $validated['submitted_date'] = now();

            DB::beginTransaction();
            // dd(current_user()->userDetail->phone);
            $submission->update($validated);

            DB::commit();
            return redirect()->route('my-society.conference.submission.index',  [$society, $conference])->with('status', 'Submission Added Successfully');
        } catch (\Exception $th) {
            DB::rollBack();

            dd($th);
        }
    }

    public function review(Request $request, $society, $conference)
    {
        $submission = Submission::whereId($request->id)->first();
        $setting = SubmissionSetting::where(['conference_id' => $conference->id, 'status' => 1])->first();
        return view('backend.participant.submission.review-modal', compact('submission', 'setting', 'conference', 'society'));
    }

    public function reviewSubmit(Request $request)
    {
        try {
            $submission = Submission::findOrFail($request->id);

            $rules = [];

            // Build validation rules
            if ($request->requestType == 1 && !$request->structure) {
                $rules += [
                    'introduction' => 'required|integer',
                    'method' => 'required|integer',
                    'result' => 'required|integer',
                    'conclusion' => 'required|integer',
                    'grammar' => 'required|integer',
                    'remarks' => 'required',
                    'abstract_content' => 'required',
                ];
            } elseif ($request->structure) {
                $rules += [
                    'overall_rating' => 'required|integer',
                    'remarks' => 'required',
                    'abstract_content' => 'required',
                ];
            }

            if ($request->requestType == 2) {
                $rules['reject_remarks'] = 'required';
            }

            $validated = $request->validate($rules);

            DB::beginTransaction();

            // Update submission
            if ($request->requestType == 1) {
                $submission->update([
                    'review_status' => 1,
                    'abstract_content' => $validated['abstract_content'],
                ]);
            } elseif ($request->requestType == 2) {
                $submission->update([
                    'review_status' => 0,
                    'reject_remark' => $validated['reject_remarks'],
                ]);
            }

            // Prepare submission rating data
            $ratingData = $request->structure
                ? ['overall_rating' => $validated['overall_rating']]
                : collect($validated)->only([
                    'grammar',
                    'conclusion',
                    'result',
                    'method',
                    'introduction'
                ])->toArray();

            // Update or create submission rating
            if ($submission->submissionRating) {
                $submission->submissionRating()->update($ratingData);
            } else {
                $submission->submissionRating()->create($ratingData);
            }

            // Insert into SubmissionDiscussion if applicable
            if ($request->requestType == 1) {
                SubmissionDiscussion::create([
                    'submission_id' => $submission->id,
                    'sender_id' => current_user()->id,
                    'remarks' => $validated['remarks'],
                    'abstract_content' => $validated['abstract_content'],
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Review Successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json(['message' => 'Review failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function viewDiscussion($society, $conference, $submission)
    {
        $discussions = SubmissionDiscussion::where('submission_id', $submission->id)->get();
        return view('backend.participant.submission.discussion.index', compact('society', 'discussions', 'conference', 'submission', 'society'));
    }

    public function convertPresentationType(Request $request, $society, $conference, $id)
    {
        $submission = Submission::whereId($id)->first();

        if ($submission->presentation_type == 2) {
            $newValue = 1;
        } else {
            $newValue = 2;
        }

        if ($request->input('confirmation') == 'yes') {
            $presentation_type_change = 1;
        }
        if ($request->input('confirmation') == 'no') {
            $presentation_type_change = 2;
        }
        $submission->update(
            [
                'presentation_type_change' => $presentation_type_change,
                'presentation_type' => $newValue
            ]
        );

        if ($request->input('confirmation') == 'yes') {
            return redirect()->back()->with('status', 'Presentation type changed successfully.');
        }
        if ($request->input('confirmation') == 'no') {
            return redirect()->back()->with('delete', 'Presentation type changed Rejected.');
        }
        // $submission->update(['presentation_type' => $newValue]);
    }
}
