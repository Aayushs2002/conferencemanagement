<?php

namespace App\Http\Controllers\Backend\Submission;

use App\Http\Controllers\Controller;
use App\Jobs\SendSubmissionBulkMailJob;
use App\Mail\Submission\ConvertPresentationTypeMail;
use App\Mail\Submission\ExpertForwardMail;
use App\Mail\Submission\SubmissionAcceptMail;
use App\Mail\Submission\SubmissionCorrectionMail;
use App\Mail\Submission\SubmissionRejectMail;
use App\Models\Conference\Expert;
use App\Models\Conference\Submission;
use App\Models\Conference\SubmissionCategoryMajorTrack;
use App\Models\Conference\SubmissionDiscussion;
use App\Models\SubmissionSetting;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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

                $expert = User::whereId($validated['expert_id'])->first();



                $mailData = [
                    'name' => $expert->fullName($expert),
                    'namePrefix' => $expert->userDetail->prefix,
                    'topic' => $submission->title
                ];

                Mail::to($expert->email)->send(new ExpertForwardMail($mailData));
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
            $validated['presenter_name'] = $submission->presenter->fullName($submission->presenter);
            $validated['namePrefix'] = $submission->presenter->userDetail->namePrefix->prefix;
            $validated['topic'] = $submission->title;
            $validated['presentation_type'] = $submission->presentation_type;
            $validated['remarks'] = $validated['remarks'];
            if ($request->request_status == 1) {
                $message = 'Request accepted successfully.';
                Mail::to($submission->presenter->email)->send(new SubmissionAcceptMail($validated));
            }
            if ($request->request_status == 2) {
                $message = 'Request updated for correction..';
                Mail::to($submission->presenter->email)->send(new SubmissionCorrectionMail($validated));
            }
            if ($request->request_status == 3) {
                $message = 'Request rejected successfully.';
                Mail::to($submission->presenter->email)->send(new SubmissionRejectMail($validated));
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

    public function convertPresentationTypeRequest($society, $conference, $id)
    {
        $submission = Submission::whereId($id)->first();


        $mailData['presenter_name'] = $submission->presenter->fullName($submission->presenter);
        $mailData['topic'] = $submission->title;
        $mailData['presentation_type'] = $submission->presentation_type;
        $mailData['namePrefix'] = $submission->presenter->userDetail->namePrefix->prefix;
        $mailData['conferenceTheme'] = $conference->conference_theme;

        Mail::to($submission->presenter->email)->send(new ConvertPresentationTypeMail($mailData));
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

    public function exportWord(Request $request, $society, $conference)
    {
        // $submissionTracks = SubmissionCategoryMajorTrack::where(['conference_id' => $conference->id, 'status' => 1])->get();
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

        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        foreach ($submissions as $submission) {
            $authors = $submission->authors;
            $mainAuthor = null;
            $names = '';
            $affiliation = [];

            if ($authors->isNotEmpty()) {
                $mainAuthor = $authors->first();
                $groupedAuthors = $authors->groupBy(['designation', 'institution', 'institution_address']);
                $duplicatedData = [];
                $nonDuplicatedData = [];
                $i = 1;

                foreach ($groupedAuthors as $designationGroup) {
                    foreach ($designationGroup as $institutionGroup) {
                        foreach ($institutionGroup as $addressGroup) {
                            foreach ($addressGroup as $record) {
                                $data = [
                                    'designation' => $record->designation ?? '',
                                    'institution' => $record->institution ?? '',
                                    'institution_address' => $record->institution_address ?? '',
                                    'countValue' => $i,
                                ];

                                if ($addressGroup->count() > 1) {
                                    $duplicatedData[$record->name][] = $data;
                                } else {
                                    $nonDuplicatedData[$record->name] = $data;
                                }
                            }
                            $i++;
                        }
                    }
                }

                $uniqueValues = [];
                foreach ($duplicatedData as $key => $value) {
                    $names .= $key . ' ' . $value[0]['countValue'] . ', ';
                    if (!in_array($value[0]['countValue'], $uniqueValues)) {
                        $affiliation[] = $value[0]['countValue'] . $value[0]['designation'] . ', ' . $value[0]['institution'] . ', ' . $value[0]['institution_address'];
                        $uniqueValues[] = $value[0]['countValue'];
                    }
                }

                foreach ($nonDuplicatedData as $key => $value) {
                    $names .= $key . ' ' . $value['countValue'] . ', ';
                    $affiliation[] = $value['countValue'] . $value['designation'] . ', ' . $value['institution'] . ', ' . $value['institution_address'];
                }

                $names = rtrim($names, ', ');
            }
            // dump($affiliation);
            // Add submission section
            $section = $phpWord->addSection();
            $presentationType = $submission->presentation_type == 1 ? 'Poster Submission' : 'Oral Submission';
            $section->addText($presentationType, ['name' => 'Times New Roman', 'size' => 18, 'bold' => true]);
            $section->addTextBreak(1);
            $topic = htmlspecialchars($submission->topic, ENT_QUOTES, 'UTF-8');
            $section->addText($topic, ['name' => 'Times New Roman', 'size' => 16, 'bold' => true]);

            $section->addTextBreak(1);
            // dump($submission->topic);

            if ($authors->isNotEmpty()) {
                $namesArray = explode(', ', $names);
                $textRun = $section->addTextRun();
                $totalNames = count($namesArray);

                foreach ($namesArray as $key => $name) {
                    $parts = explode(' ', $name);
                    $number = array_pop($parts);
                    $person = implode(' ', $parts);

                    $textRun->addText($person . ' ', ['name' => 'Times New Roman', 'size' => 14, 'bold' => true]);
                    $textRun->addText($number, ['superscript' => true, 'name' => 'Times New Roman', 'size' => 10, 'bold' => true]);

                    if ($key !== $totalNames - 1) {
                        $textRun->addText(", ", ['name' => 'Times New Roman', 'size' => 14]);
                    }
                }
                $textRun->getParagraphStyle()->setLineHeight(0.8);

                foreach ($affiliation as $affiliationText) {
                    $textRunAffiliation = $section->addTextRun();
                    $textRunAffiliation->addText(substr($affiliationText, 0, 1), ['superscript' => true, 'name' => 'Times New Roman', 'size' => 10]);
                    $affiliationText = htmlspecialchars($affiliationText, ENT_QUOTES, 'UTF-8');

                    $textRunAffiliation->addText(substr($affiliationText, 1), ['name' => 'Times New Roman', 'size' => 12]);
                    $textRunAffiliation->getParagraphStyle()->setLineHeight(0.8);
                }
            }

            if ($mainAuthor) {
                $section->addTextBreak(1);
                $section->addText('Correspondence', ['name' => 'Times New Roman', 'size' => 14, 'bold' => true]);
                $section->addText($mainAuthor->name, ['name' => 'Times New Roman', 'size' => 12]);
                $section->addText($mainAuthor->designation, ['name' => 'Times New Roman', 'size' => 12]);
                $institution = htmlspecialchars($mainAuthor->institution, ENT_QUOTES, 'UTF-8');
                $section->addText($institution, ['name' => 'Times New Roman', 'size' => 12]);
                $section->addText($mainAuthor->institution_address, ['name' => 'Times New Roman', 'size' => 12]);
                $section->addText('Email: ' . $mainAuthor->email, ['name' => 'Times New Roman', 'size' => 12]);
                $section->addText('Phone: ' . $mainAuthor->phone, ['name' => 'Times New Roman', 'size' => 12]);
                $section->addTextBreak(1);
            }

            $section->addText('Received Date: ' . Carbon::parse($submission->submitted_date)->format('d M, Y'), ['name' => 'Times New Roman', 'size' => 12]);
            if (!empty($submission->expert)) {
                $section->addText('Reviewer: ' . $submission->expert->fullName($submission, 'expert'), ['name' => 'Times New Roman', 'size' => 12]);
            }
            $section->addTextBreak(1);

            $section->addText('Abstract', ['name' => 'Times New Roman', 'size' => 14, 'bold' => true]);
            $section->addText(html_entity_decode(strip_tags($submission->abstract_content)), ['name' => 'Times New Roman', 'size' => 12]);
            $section->addTextBreak(1);

            $keywordsRun = $section->addTextRun();
            $keywordsRun->addText('Keywords: ', ['name' => 'Times New Roman', 'size' => 12, 'bold' => true]);
            $keywords = htmlspecialchars($submission->keywords, ENT_QUOTES, 'UTF-8');

            $keywordsRun->addText($keywords, ['name' => 'Times New Roman', 'size' => 12]);

            // Add page break between submissions
            $section->addPageBreak();
        }

        // Save the file
        $filename = 'Bulk_Submissions_' . now()->format('Ymd_His') . '.docx';
        $filePath = public_path('downloads/' . $filename);

        if (!file_exists(public_path('downloads'))) {
            mkdir(public_path('downloads'), 0777, true);
        }

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}
