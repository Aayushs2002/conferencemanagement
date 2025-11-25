<?php

namespace App\Http\Controllers\Backend\Submission;

use App\Http\Controllers\Controller;
use App\Http\Requests\Participant\SubmissionRequest;
use App\Jobs\SendSubmissionBulkMailJob;
use App\Mail\Submission\ConvertPresentationTypeMail;
use App\Mail\Submission\ExpertForwardMail;
use App\Mail\Submission\SubmissionAcceptMail;
use App\Mail\Submission\SubmissionCorrectionMail;
use App\Mail\Submission\SubmissionRejectMail;
use App\Models\Conference\Author;
use App\Models\Conference\ArticleType;
use App\Models\Conference\Expert;
use App\Models\Conference\Submission;
use App\Models\Conference\SubmissionCategoryMajorTrack;
use App\Models\Conference\SubmissionDiscussion;
use App\Models\Conference\SubmissionRating;
use App\Models\SubmissionSetting;
use App\Models\Template\EmailTemplate;
use App\Models\User;
use App\Services\File\FileService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SubmissionController extends Controller
{
    public function __construct(protected FileService $file_service) {}

    public function index(Request $request, $society, $conference)
    {
        // $conferenceDetail = conference_detail();

        // if (empty($conferenceDetail)) {
        //     return redirect()->route('dashboard'); 
        // }
        $submissionTracks = SubmissionCategoryMajorTrack::where(['conference_id' => $conference->id, 'status' => 1])->get();
        $articleTypes = ArticleType::where(['conference_id' => $conference->id, 'status' => 1])->get();
        $submission_setting = SubmissionSetting::where('conference_id', $conference->id)->select('scoring_allowed')->first();
        // dd($submission_setting);
        $query = Submission::with('discussions')->where(['conference_id' => $conference->id, 'status' => 1]);
        if ($request->filled('article_type_id')) {
            $query->where('article_type_id', $request->article_type_id);
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

        // Track duplicate submissions by user
        $userSubmissions = [];
        foreach ($submissions as $submission) {
            $userId = $submission->user_id;
            if (!isset($userSubmissions[$userId])) {
                $userSubmissions[$userId] = [
                    'count' => 0,
                    'presentation_types' => []
                ];
            }
            $userSubmissions[$userId]['count']++;
            if (!in_array($submission->presentation_type, $userSubmissions[$userId]['presentation_types'])) {
                $userSubmissions[$userId]['presentation_types'][] = $submission->presentation_type;
            }
        }

        // Add row color class to each submission
        foreach ($submissions as $submission) {
            $userId = $submission->user_id;
            $userInfo = $userSubmissions[$userId];

            if ($userInfo['count'] > 1) {
                // User has multiple submissions
                if (count($userInfo['presentation_types']) > 1) {
                    // Different presentation types - RED
                    $submission->row_color = 'table-danger';
                } else {
                    // Same presentation type
                    if ($submission->presentation_type == 1) {
                        // Poster - GREEN
                        $submission->row_color = 'table-success';
                    } else {
                        // Oral - YELLOW
                        $submission->row_color = 'table-warning';
                    }
                }
            } else {
                // Single submission - no color
                $submission->row_color = '';
            }
        }

        return view('backend.submission.submission.index', compact('submissions', 'submissionTracks', 'conference', 'society', 'submission_setting', 'articleTypes'));
    }

    public function show(Request $request)
    {
        $submission = Submission::whereId($request->id)->first();

        return view('backend.submission.submission.view', compact('submission'));
    }

    public function edit($society, $conference, $submission)
    {
        $setting = SubmissionSetting::where('conference_id', $conference->id)
            ->select('abstract_word_limit', 'key_word_limit', 'deadline', 'attachment_name')
            ->first();
        if (!$setting) {
            return redirect()->back()->with('delete', 'Submission settings not found.');
        }
        if (is_past($setting->deadline)) {
            return redirect()->back()->with('delete', 'Submission date has ended.');
        }
        $submissionTracks = SubmissionCategoryMajorTrack::where(['conference_id' => $conference->id, 'status' => 1])->get();
        $articleTypes = ArticleType::where(['conference_id' => $conference->id, 'status' => 1])->get();

        return view('backend.submission.submission.create', compact('society', 'conference', 'submissionTracks', 'setting', 'submission', 'articleTypes'));
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

            // $validated['user_id'] = current_user()->id;
            // $validated['conference_id'] = $conference->id;
            // $validated['submitted_date'] = now();

            DB::beginTransaction();
            // dd(current_user()->userDetail->phone);
            $submission->update($validated);

            DB::commit();
            return redirect()->route('submission.index',  [$society, $conference])->with('status', 'Submission Added Successfully');
        } catch (\Exception $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('delete', 'Internal Server Error');
            // dd($th);
        }
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

            // Dynamic validation based on whether sections exist
            $rules = ['expert_id' => 'required'];

            $submission = Submission::whereId($request->id)->first();
            if ($request->has('sections') && is_array($request->sections)) {
                // Validate sections
                foreach ($request->sections as $index => $section) {
                    $rules["sections.{$index}.content"] = 'required|string';
                }
            } else {
                // Validate abstract content
                $rules['abstract_content'] = 'required';
            }

            $validated = $request->validate($rules);

            if ($validated) {
                if ($validated['expert_id'] == $submission->user_id) {
                    throw new Exception("Presenter and Expert should not be same.", 1);
                }

                $validated['forward_expert'] = 1;

                // Handle sections or abstract_content
                if ($request->has('sections') && is_array($request->sections)) {
                    $validated['sections'] = $request->sections;
                    $validated['abstract_content'] = null; // Clear abstract content when using sections
                } else {
                    $validated['sections'] = null; // Clear sections when using abstract content
                }

                $expert = User::whereId($validated['expert_id'])->first();
                $template = EmailTemplate::where(['conference_id' => $submission->conference_id, 'key' => 1])->first();

                $mailData = [
                    'name' => $expert->fullName($expert),
                    'namePrefix' => $expert->userDetail->prefix,
                    'topic' => $submission->title,
                    'conference_name' => $submission->conference->conference_name
                ];
                $data = [
                    'submission_topic' => $submission->title,
                ];
                DB::beginTransaction();
                $subject = parseTemplate($template?->subject, $data);
                $body = parseTemplate($template?->body, $data);
                Mail::to($expert->email)->send(new ExpertForwardMail($mailData, $subject, $body, $submission->conference->conference_name));
                $validated['review_status'] = 0;
                $submission->update($validated);
                logActivity($submission->conference_id, 'Assign Expert', $expert->fullName($expert) . 'is assign as a expert to ' . $submission->title);
                DB::commit();
            }
        } catch (Exception $e) {
            $type = 'error';
            $message = $e->getMessage();
            DB::rollBack();
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
            $validated['conference_name'] = $submission->conference->conference_name;
            $data = [
                'submission_topic' => $submission->title,
            ];

            if ($request->request_status == 1) {
                $message = 'Request accepted successfully.';
                $template = EmailTemplate::where(['conference_id' => $submission->conference_id, 'key' => 2])->first();
                $subject = parseTemplate($template?->subject, $data);
                $body = parseTemplate($template?->body, $data);

                Mail::to($submission->presenter->email)->send(new SubmissionAcceptMail($validated, $subject, $body, $submission->conference->conference_name));
            }
            if ($request->request_status == 2) {
                $message = 'Request updated for correction..';
                $template = EmailTemplate::where(['conference_id' => $submission->conference_id, 'key' => 3])->first();
                $subject = parseTemplate($template?->subject, $data);
                $body = parseTemplate($template?->body, $data);
                Mail::to($submission->presenter->email)->send(new SubmissionCorrectionMail($validated, $subject, $body, $submission->conference->conference_name));
            }
            if ($request->request_status == 3) {
                $message = 'Request rejected successfully.';
                $template = EmailTemplate::where(['conference_id' => $submission->conference_id, 'key' => 4])->first();
                $data['reject_remark'] = $validated['remarks'];
                $subject = parseTemplate($template?->subject, $data);
                $body = parseTemplate($template?->body, $data);
                Mail::to($submission->presenter->email)->send(new SubmissionRejectMail($validated, $subject, $body, $submission->conference->conference_name));
            }
            DB::beginTransaction();

            $submission->update(['request_status' => $validated['request_status']]);


            // insert into table 2
            $validated['submission_id'] = $request->id;
            $validated['sender_id'] = current_user()->id;
            SubmissionDiscussion::create($validated);
            logActivity(
                $submission->conference_id,
                'Change Request Status',
                $submission->title . ' status change to ' . (
                    $request->request_status == 1 ? 'Accepted' : ($request->request_status == 2 ? 'Correction' : ($request->request_status == 3 ? 'Rejected' : 'Unknown'))
                )
            );

            DB::commit();
            return response()->json(['message' => $message]);
        } catch (Exception $e) {
            DB::rollBack();
            // throw $e;
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function viewDiscussion($society, $conference, $submission)
    {
        $discussions = SubmissionDiscussion::where(['submission_id' => $submission->id, 'status' => 1])->get();
        return view('backend.submission.discussion.index', compact('discussions', 'submission'));
    }

    public function convertPresentationTypeRequest($society, $conference, $id)
    {
        // dd($id);
        try {
            //code...
            $submission = Submission::whereId($id)->first();


            $mailData['presenter_name'] = $submission->presenter->fullName($submission->presenter);
            $mailData['topic'] = $submission->title;
            $mailData['presentation_type'] = $submission->presentation_type;
            $mailData['namePrefix'] = $submission->presenter->userDetail->namePrefix->prefix;
            $mailData['conferenceTheme'] = $conference->conference_theme;
            $mailData['conference_name'] = $conference->conference_name;

            DB::beginTransaction();

            Mail::to($submission->presenter->email)->send(new ConvertPresentationTypeMail($mailData, $conference->conference_name));
            $submission->update(['presentation_type_change' => 0]);
            logActivity(
                $submission->conference_id,
                'Convert Presentation Type',
                $submission->title . ' presentation type convert request is sent from ' .
                    ($submission->presentation_type == 1 ? 'Poster' : 'Oral')
            );
            DB::commit();


            // $submission->update(['presentation_type' => $newValue]);
            return redirect()->back()->with('status', 'Presentation type changed successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('delete', 'Something went wrong.');
            //throw $th;
        }
    }

    public function viewScore(Request $request)
    {
        $submission = Submission::whereId($request->id)->first();

        return view('backend.submission.submission.view-score', compact('submission'));
    }
    public function destroy($society, $conference, Submission $submission)
    {

        DB::beginTransaction();
        try {
            if ($submission->image) {
                $this->file_service->deleteFile($submission->image, 'participant/submission/image');
            }

            Author::where('submission_id', $submission->id)->delete();

            SubmissionRating::where('submission_id', $submission->id)->delete();

            SubmissionDiscussion::where('submission_id', $submission->id)->delete();

            $submission->delete();

            DB::commit();

            return redirect()->back()->with('status', 'Submission Successfully Deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('delete', 'Internal Server Error.');
        }
    }

    public function getAuthors($society, $conference, $id)
    {
        $submission = Submission::with('authors')->find($id);

        if (!$submission) {
            return response()->json([]);
        }

        return response()->json($submission->authors);
    }

    public function sendMail($society, $conference)
    {
        return view('backend.submission.submission.send-mail', compact('society', 'conference'));
    }

    public function sendMailSubmit(Request $request, $society, $conference)
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
                SendSubmissionBulkMailJob::dispatch($user, $validated['subject'], $validated['mail_content'], $conference->conference_name);
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
        if ($request->filled('article_type_id')) {
            $query->where('article_type_id', $request->article_type_id);
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
