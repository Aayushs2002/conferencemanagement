<?php

namespace App\Http\Controllers\Backend\Participant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Participant\SubmissionRequest;
use App\Mail\Submission\SubmissionSubmittedToUserMail;
use App\Models\Conference\Author;
use App\Models\Conference\ArticleType;
use App\Models\Conference\ArticleTypeSetting;
use App\Models\Conference\Submission;
use App\Models\Conference\SubmissionCategoryMajorTrack;
use App\Models\Conference\Contribution;
use App\Models\Conference\SubmissionDiscussion;
use App\Models\SubmissionSetting;
use App\Models\Template\EmailTemplate;
use App\Models\User;
use App\Services\File\FileService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
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
            // ->orWhere(function ($query) use ($conference) {
            //     $query->where('conference_id', $conference->id)
            //         ->where('expert_id', current_user()->id);
            // })
            ->get();
        $submissionSetting = SubmissionSetting::where('conference_id', $conference->id)->first();
        return view('backend.participant.submission.index', compact('conference', 'submissions', 'society', 'submissionSetting'));
    }

    public function create($society, $conference)
    {

        $setting = SubmissionSetting::where('conference_id', $conference->id)
            ->select('abstract_word_limit', 'key_word_limit', 'deadline', 'attachment_name', 'attachment_required', 'abstract_guidelines', 'competition_enabled', 'contribution_enabled', 'copy_paste_allowed', 'show_collaborative_partner')
            ->first();
        // dd($setting);
        if (!$setting) {
            return redirect()->back()->with('delete', 'Submission settings not found.');
        }
        if (is_past($setting->deadline)) {
            return redirect()->back()->with('delete', 'Submission date has ended.');
        }
        $submissionTracks = SubmissionCategoryMajorTrack::where(['conference_id' => $conference->id, 'status' => 1])->get();

        // Get the current user's society membership
        $userSociety = DB::table('user_societies')
            ->where('user_id', current_user()->id)
            ->where('society_id', $society->id)
            ->first();

        if (!$userSociety) {
            return redirect()->route('dashboard')->with('delete', 'You are not a member of this society.');
        }

        $userMemberTypeId = $userSociety->member_type_id;

        $articleTypes = ArticleType::with('setting')
            ->where(['conference_id' => $conference->id, 'status' => 1])
            ->orderBy('display_order', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->filter(function ($articleType) use ($userMemberTypeId) {
                $allowedIds = $articleType->setting->allowed_member_type_ids ?? null;
                // No restriction configured — visible to everyone
                if (empty($allowedIds)) {
                    return true;
                }
                // Restriction applies — check if user's member type is in the list
                return in_array($userMemberTypeId, $allowedIds);
            })
            ->values();

        // Get contributions if enabled
        $contributions = [];
        $contributionEnabled = false;
        if ($setting && $setting->contribution_enabled) {
            $contributionEnabled = true;
            $contributions = Contribution::where([
                'conference_id' => $conference->id,
                'status' => 1
            ])->orderBy('name', 'asc')->get();
        }

        // Build collaborative partner options from conference partner logos
        $collaborativePartners = [];
        if ($setting && $setting->show_collaborative_partner) {
            $collaborativePartners = collect($conference->partner_logos ?? [])
                ->filter(fn($p) => is_array($p) && !empty($p['abbreviation']))
                ->values();
        }

        return view('backend.participant.submission.create', compact('society', 'conference', 'submissionTracks', 'setting', 'articleTypes', 'contributions', 'contributionEnabled', 'collaborativePartners'));
    }

    public function store(SubmissionRequest $request, $society, $conference)
    {
        try {
            $validated = $request->all();
            // dd($validated);
            $setting = SubmissionSetting::where('conference_id', $conference->id)->select('abstract_word_limit', 'key_word_limit', 'authors_limit')->first();

            // Check author limit
            if ($setting && $setting->authors_limit > 0) {
                $totalAuthors = 1 + (isset($request->authors) && is_array($request->authors) ? count($request->authors) : 0);
                if ($totalAuthors > $setting->authors_limit) {
                    return redirect()->back()->withInput()->with('delete', 'Author limit exceeded. Maximum allowed: ' . $setting->authors_limit);
                }
            }

            if (!empty($validated['keywords']) && !empty($setting->key_word_limit)) {
                $keywordsCount = count(explode(',', $request->keywords));
                // dd($validated['keywords']);
                if ($keywordsCount > $setting->key_word_limit) {
                    return redirect()->back()->withInput()->with('delete', 'Keywords word limit exceeded.');
                }
                $keywordArray = json_decode($request->keywords, true);
                $validated['keywords'] = is_array($keywordArray)
                    ? implode(',', array_column($keywordArray, 'value'))
                    : '';
            }

            // Handle dynamic sections or regular abstract content
            if (!empty($validated['sections'])) {
                // Store sections as JSON and also combine them into abstract_content for backward compatibility
                // $sectionsContent = [];
                // foreach ($validated['sections'] as $section) {
                //     if (!empty($section['content'])) {
                //         $sectionsContent[] = $section['content'];
                //     }
                // }
                // }
                // $validated['abstract_content'] = implode("\n\n", $sectionsContent);
                $validated['abstract_content'] = null;
            } else {
                // If no sections, ensure sections field is null
                $validated['sections'] = null;
            }

            // Handle conflict of interest
            if (isset($validated['has_conflict_of_interest'])) {
                if ($validated['has_conflict_of_interest'] === 'no') {
                    $validated['conflict_of_interest'] = null;
                }
                unset($validated['has_conflict_of_interest']);
            }

            // Handle source of funding
            if (isset($validated['has_source_of_funding'])) {
                if ($validated['has_source_of_funding'] === 'no') {
                    $validated['source_of_funding'] = null;
                }
                unset($validated['has_source_of_funding']);
            }

            // Validate word count for abstract content
            if (!empty($validated['abstract_content'])) {
                $abstractWordCount = str_word_count(strip_tags($validated['abstract_content']));
                if (!empty($setting->abstract_word_limit) && $abstractWordCount > $setting->abstract_word_limit) {
                    return redirect()->back()->withInput()->with('delete', 'Abstract word limit exceeded.');
                }
            }

            if (!empty($validated['image'])) {
                $validated['image'] = $this->file_service->fileUpload($validated['image'], 'diagram', 'participant/submission/image');
            }

            // Clear video_link when presentation type is not Video
            if (($validated['presentation_type'] ?? null) != 3) {
                $validated['video_link'] = null;
            }

            $authUser = User::whereId(current_user()->id)->first();
            $validated['user_id'] = current_user()->id;
            $validated['conference_id'] = $conference->id;
            $validated['submitted_date'] = now();
            $validated['main_author'] = $validated['main_author'] ?? 0;
            $validated['main_presenter'] = $validated['main_presenter'] ?? 0;

            // Check if any co-author is marked as main author
            $coAuthorIsMainAuthor = false;
            $coAuthorIsMainPresenter = false;
            if ($request->has('authors') && is_array($request->authors)) {
                foreach ($request->authors as $authorData) {
                    if (isset($authorData['main_author']) && $authorData['main_author'] == 1) {
                        $coAuthorIsMainAuthor = true;
                    }
                    if (isset($authorData['main_presenter']) && $authorData['main_presenter'] == 1) {
                        $coAuthorIsMainPresenter = true;
                    }
                }
            }

            // Validation: At least one author must be main author
            if ($validated['main_author'] == 0 && !$coAuthorIsMainAuthor) {
                return redirect()->back()->withInput()->with('delete', 'At least one author must be designated as the main author.');
            }

            // Validation: Only one author can be main author
            if ($validated['main_author'] == 1 && $coAuthorIsMainAuthor) {
                return redirect()->back()->withInput()->with('delete', 'Only one author can be the main author.');
            }

            // Validation: At least one author must be main presenter
            if ($validated['main_presenter'] == 0 && !$coAuthorIsMainPresenter) {
                return redirect()->back()->withInput()->with('delete', 'At least one author must be designated as the main presenter.');
            }

            // Validation: Only one author can be main presenter
            if ($validated['main_presenter'] == 1 && $coAuthorIsMainPresenter) {
                return redirect()->back()->withInput()->with('delete', 'Only one author can be the main presenter.');
            }

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

            $chosenPartner = $validated['collaborative_partner'] ?? null;
            $allTemplates  = EmailTemplate::where(['conference_id' => $conference->id, 'key' => 1])->get();

            // 1. Prefer template whose partner_filter contains the chosen partner
            // 2. Fallback to the "all submitters" template (empty/null partner_filter)
            // 3. If neither exists, $template is null → blade default content is used
            $template = $allTemplates->first(function ($t) use ($chosenPartner) {
                    return !empty($t->partner_filter) && $chosenPartner && in_array($chosenPartner, $t->partner_filter);
                }) ?? $allTemplates->first(function ($t) {
                    return empty($t->partner_filter);
                }) ?? null;

            $userMailData = [
                'name' => $authUser->fullName($authUser),
                'namePrefix' => $authUser->userDetail->namePrefix->prefix,
                'topic' => $validated['title'],
                'conferenceTheme' => $conference->conference_theme,
                'societyEmail' => $society->users->where('type', 2)->value('email'),
                'societyName' => $society->abbreviation,
                'conferenceDate' => $conferenceDate,
                'conferenceName' => $conference->conference_name,
                'conferenceEmail' => $conference->conference_email,
            ];

            $data = [
                'submission_topic' => $validated['title'],
                'conference_theme' => $conference->conference_theme,
                'conference_date' => $conferenceDate,
                'society_email' => $society->users->where('type', 2)->value('email'),
            ];

            $subject = parseTemplate($template?->subject, $data);
            $body = parseTemplate($template?->body, $data);
            
            // Send email with CC if configured
            $mail = Mail::to($authUser->email);
            
            // Add CC emails if configured
            $conferenceSetting = $conference->conferenceSetting;
            if ($conferenceSetting && !empty($conferenceSetting->submission_cc_emails)) {
                $ccEmails = getCcEmails($conferenceSetting->submission_cc_emails);
                if (!empty($ccEmails)) {
                    $mail->cc($ccEmails);
                }
            }
            
            $mail->send(new SubmissionSubmittedToUserMail($userMailData, $subject, $body, $conference->conference_name));
            DB::beginTransaction();
            // dd('dd');   
            $submission = Submission::create($validated);
            $validated['submission_id'] = $submission->id;
            $validated['name'] = current_user()->fullName(current_user());
            $validated['email'] = current_user()->email;
            $validated['phone'] = current_user()->userDetail->phone;
            $validated['designation'] = current_user()->userDetail?->designation?->designation;
            $validated['institution'] = current_user()->userDetail?->institution?->name;
            $validated['institution_address'] = current_user()->userDetail->institute_address;

            $author = Author::create($validated);

            // Create co-authors
            if ($request->has('authors') && is_array($request->authors)) {
                foreach ($request->authors as $authorData) {
                    $authorData['submission_id'] = $submission->id;
                    $authorData['conference_id'] = $conference->id;
                    // Set main_author and main_presenter based on checkbox values
                    $authorData['main_author'] = isset($authorData['main_author']) && $authorData['main_author'] == 1 ? 1 : 0;
                    $authorData['main_presenter'] = isset($authorData['main_presenter']) && $authorData['main_presenter'] == 1 ? 1 : 0;

                    // Map contribution_other_text to contribution_other for database
                    if (isset($authorData['contribution_other_text'])) {
                        $authorData['contribution_other'] = $authorData['contribution_other_text'];
                        unset($authorData['contribution_other_text']);
                    }
                    unset($authorData['contribution_other_checkbox']);

                    $coAuthor = Author::create($authorData);

                    // Sync contributions
                    if (isset($authorData['contributions'])) {
                        $coAuthor->contributions()->sync($authorData['contributions']);
                    }
                }
            }
            DB::commit();
            return redirect()->route('my-society.conference.submission.index', [$society, $conference, $submission])->with('status', 'Submission Added Successfully');
        } catch (\Exception $th) {
            // dd($th);
            DB::rollBack();

            // dd($th);
            return redirect()->back()->withInput()->with('delete', 'Internal Server Error');
        }
    }


    public function view(Request $request, $society, $conference)
    {
        $submission = Submission::whereId($request->id)->first();
        return view('backend.participant.submission.view', compact('submission'));
    }

    public function uploadSlide(Request $request, $society, $conference, Submission $submission)
    {
        if ((int) $submission->conference_id !== (int) $conference->id) {
            abort(404);
        }

        if ((int) $submission->user_id !== (int) current_user()->id) {
            abort(403);
        }

        if (! ((int) $submission->presentation_type === 2 && (int) $submission->request_status === 1)) {
            return redirect()->back()->with('delete', 'Slide upload is only available for accepted oral submissions.');
        }

        $request->validate(
            [
                'slide_file' => 'required|file|extensions:ppt,pptx,pdf|max:20480',
            ],
            [
                'slide_file.required' => 'Please select a slide file to upload.',
                'slide_file.file' => 'The selected file is invalid.',
                'slide_file.extensions' => 'Only PPT, PPTX, and PDF files are allowed.',
                'slide_file.max' => 'The slide file must not be greater than 20 MB.',
                'slide_file.uploaded' => 'File upload failed. Please try a smaller file or check your internet connection.',
            ]
        );

        try {
            $slideFileInput = $request->file('slide_file');
            if (! $slideFileInput || ! $slideFileInput->isValid()) {
                return redirect()->back()->withInput()->with('delete', 'The uploaded slide file is corrupted or incomplete. Please upload again.');
            }

            DB::beginTransaction();

            if (! empty($submission->slide_file)) {
                $this->file_service->deleteFile($submission->slide_file, 'participant/submission/slides');
            }

            $slideFile = $this->file_service->fileUpload($slideFileInput, 'slide', 'participant/submission/slides');
            $submission->update([
                'slide_file' => $slideFile,
            ]);

            DB::commit();

            return redirect()->back()->with('status', 'Slide uploaded successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Slide upload failed', [
                'submission_id' => $submission->id,
                'conference_id' => $conference->id,
                'user_id' => current_user()->id,
                'error' => $th->getMessage(),
            ]);

            return redirect()->back()->withInput()->with('delete', 'Unable to process this file. Please upload a valid PDF/PPT/PPTX (max 20MB).');
        }
    }

    public function edit($society, $conference, $submission)
    {
        // dd($submission);
        $submission->load(['authors.contributions']);
        $setting = SubmissionSetting::where('conference_id', $conference->id)
            ->select('abstract_word_limit', 'key_word_limit', 'deadline', 'attachment_name', 'attachment_required', 'abstract_guidelines', 'competition_enabled', 'contribution_enabled', 'copy_paste_allowed', 'show_collaborative_partner')
            ->first();
        if (!$setting) {
            return redirect()->back()->with('delete', 'Submission settings not found.');
        }
        if (is_past($setting->deadline)) {
            return redirect()->back()->with('delete', 'Submission date has ended.');
        }
        $submissionTracks = SubmissionCategoryMajorTrack::where(['conference_id' => $conference->id, 'status' => 1])->get();

        // Get the current user's society membership
        $userSociety = DB::table('user_societies')
            ->where('user_id', current_user()->id)
            ->where('society_id', $society->id)
            ->first();

        if (!$userSociety) {
            return redirect()->route('dashboard')->with('delete', 'You are not a member of this society.');
        }

        $userMemberTypeId = $userSociety->member_type_id;

        $articleTypes = ArticleType::with('setting')
            ->where(['conference_id' => $conference->id, 'status' => 1])
            ->orderBy('display_order', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->filter(function ($articleType) use ($userMemberTypeId) {
                $allowedIds = $articleType->setting->allowed_member_type_ids ?? null;
                if (empty($allowedIds)) {
                    return true;
                }
                return in_array($userMemberTypeId, $allowedIds);
            })
            ->values();

        // Get contributions if enabled
        $contributions = [];
        $contributionEnabled = false;
        if ($setting && $setting->contribution_enabled) {
            $contributionEnabled = true;
            $contributions = Contribution::where([
                'conference_id' => $conference->id,
                'status' => 1
            ])->orderBy('name', 'asc')->get();
        }
        // dd($contributions);
        // dd($contributionEnabled);

        // Build collaborative partner options from conference partner logos
        $collaborativePartners = [];
        if ($setting && $setting->show_collaborative_partner) {
            $collaborativePartners = collect($conference->partner_logos ?? [])
                ->filter(fn($p) => is_array($p) && !empty($p['abbreviation']))
                ->values();
        }

        return view('backend.participant.submission.create', compact('society', 'conference', 'submissionTracks', 'setting', 'submission', 'articleTypes', 'contributions', 'contributionEnabled', 'collaborativePartners'));
    }

    public function update(SubmissionRequest $request, $society, $conference, $submission)
    {
        // dd($request->all());
        try {
            $validated = $request->validated();
            $setting = SubmissionSetting::where('conference_id', $conference->id)->select('abstract_word_limit', 'key_word_limit', 'authors_limit')->first();

            // Check author limit
            if ($setting && $setting->authors_limit > 0) {
                $totalAuthors = 1 + (isset($request->authors) && is_array($request->authors) ? count($request->authors) : 0);
                if ($totalAuthors > $setting->authors_limit) {
                    return redirect()->back()->withInput()->with('delete', 'Author limit exceeded. Maximum allowed: ' . $setting->authors_limit);
                }
            }

            if (!empty($validated['keywords']) && !empty($setting->key_word_limit)) {
                $keywordsCount = count(explode(',', $request->keywords));
                if ($keywordsCount > $setting->key_word_limit) {
                    return redirect()->back()->withInput()->with('delete', 'Keywords word limit exceeded.');
                }
                $keywordArray = json_decode($request->keywords, true);
                $validated['keywords'] = is_array($keywordArray)
                    ? implode(',', array_column($keywordArray, 'value'))
                    : '';
            }

            // Handle dynamic sections or regular abstract content
            if (!empty($validated['sections'])) {
                // Store sections as JSON and also combine them into abstract_content for backward compatibility
                // $sectionsContent = [];
                // foreach ($validated['sections'] as $section) {
                //     if (!empty($section['content'])) {
                //         $sectionsContent[] = $section['content'];
                //     }
                // }
                // $validated['abstract_content'] = implode("\n\n", $sectionsContent);
                $validated['abstract_content'] = null;
            } else {
                // If no sections, ensure sections field is null and keep only abstract_content
                $validated['sections'] = null;
            }

            // Handle conflict of interest
            if (isset($validated['has_conflict_of_interest'])) {
                if ($validated['has_conflict_of_interest'] === 'no') {
                    $validated['conflict_of_interest'] = null;
                }
                unset($validated['has_conflict_of_interest']);
            }

            // Handle source of funding
            if (isset($validated['has_source_of_funding'])) {
                if ($validated['has_source_of_funding'] === 'no') {
                    $validated['source_of_funding'] = null;
                }
                unset($validated['has_source_of_funding']);
            }

            // Validate word count for abstract content
            if (!empty($validated['abstract_content'])) {
                $abstractWordCount = str_word_count(strip_tags($validated['abstract_content']));
                if (!empty($setting->abstract_word_limit) && $abstractWordCount > $setting->abstract_word_limit) {
                    return redirect()->back()->withInput()->with('delete', 'Abstract word limit exceeded.');
                }
            }

            if (!empty($validated['image'])) {
                $this->file_service->deleteFile($submission->image, 'participant/submission/image');
                $validated['image'] = $this->file_service->fileUpload($validated['image'], 'diagram', 'participant/submission/image');
            }

            // Clear video_link when presentation type is not Video
            if (($validated['presentation_type'] ?? null) != 3) {
                $validated['video_link'] = null;
            }

            DB::beginTransaction();

            // Check if any co-author is marked as main author
            $coAuthorIsMain = false;
            if ($request->has('authors') && is_array($request->authors)) {
                foreach ($request->authors as $authorData) {
                    if (isset($authorData['main_author']) && $authorData['main_author'] == 1) {
                        $coAuthorIsMain = true;
                        break;
                    }
                }
            }

            // Get the current user's author record for this submission
            $submitterAuthor = Author::where('submission_id', $submission->id)
                ->where('email', $submission->user->email)
                ->first();

            // Determine if submitter should be main author
            $submitterIsMain = !$coAuthorIsMain; // If no co-author is main, submitter must be main

            // Update submitter's main_author status
            if ($submitterAuthor) {
                $submitterAuthor->update(['main_author' => $submitterIsMain ? 1 : 0]);
            }

            $submission->update($validated);

            // Handle co-authors
            $existingAuthorIds = Author::where('submission_id', $submission->id)
                ->where('email', '!=', $submission->user->email) // Exclude main author (user)
                ->pluck('id')
                ->toArray();

            $processedAuthorIds = [];

            if ($request->has('authors') && is_array($request->authors)) {
                foreach ($request->authors as $authorData) {
                    // Set main_author based on checkbox value
                    $authorData['main_author'] = isset($authorData['main_author']) && $authorData['main_author'] == 1 ? 1 : 0;

                    // Map contribution_other_text to contribution_other for database
                    if (isset($authorData['contribution_other_text'])) {
                        $authorData['contribution_other'] = $authorData['contribution_other_text'];
                        unset($authorData['contribution_other_text']);
                    } else {
                        $authorData['contribution_other'] = null;
                    }
                    unset($authorData['contribution_other_checkbox']);

                    if (isset($authorData['id']) && $authorData['id']) {
                        // Update existing
                        $author = Author::find($authorData['id']);
                        if ($author) {
                            $author->update($authorData);
                            $processedAuthorIds[] = $author->id;

                            // Sync contributions
                            if (isset($authorData['contributions'])) {
                                $author->contributions()->sync($authorData['contributions']);
                            } else {
                                $author->contributions()->detach();
                            }
                        }
                    } else {
                        // Create new
                        $authorData['submission_id'] = $submission->id;
                        $authorData['conference_id'] = $conference->id;
                        $author = Author::create($authorData);
                        $processedAuthorIds[] = $author->id;

                        // Sync contributions
                        if (isset($authorData['contributions'])) {
                            $author->contributions()->sync($authorData['contributions']);
                        }
                    }
                }
            }

            // Delete removed authors
            $authorsToDelete = array_diff($existingAuthorIds, $processedAuthorIds);
            if (!empty($authorsToDelete)) {
                Author::destroy($authorsToDelete);
            }

            DB::commit();
            return redirect()->route('my-society.conference.submission.index', [$society, $conference])->with('status', 'Submission Added Successfully');
        } catch (\Exception $th) {
            // dd($th);
            DB::rollBack();
            return redirect()->back()->withInput()->with('delete', 'Internal Server Error');
        }
    }

    public function viewScore(Request $request)
    {
        $submission = Submission::with('articleType.setting', 'submissionRating')->whereId($request->id)->first();

        // Get article type setting sections if available
        $articleTypeSections = null;
        if ($submission->articleType && $submission->articleType->setting) {
            $articleTypeSections = $submission->articleType->setting->sections;
        }

        return view('backend.submission.submission.view-score', compact('submission', 'articleTypeSections'));
    }

    public function submissionReview($society, $conference)
    {
        $submissions = Submission::with('discussions')
            ->where('conference_id', $conference->id)
            ->where('expert_id', current_user()->id)
            ->where('status', 1)
            ->get();
        $submissionSetting = SubmissionSetting::where('conference_id', $conference->id)->first();
        return view('backend.participant.submission.review.index', compact('conference', 'submissions', 'society', 'submissionSetting'));
    }

    public function review(Request $request, $society, $conference)
    {
        // dd('ok');
        $submission = Submission::with('articleType.setting')->whereId($request->id)->first();
        
        // Check if review deadline has passed
        if ($submission->review_deadline) {
            $deadline = \Carbon\Carbon::parse($submission->review_deadline);
            if (\Carbon\Carbon::now()->greaterThan($deadline)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Review deadline has expired. Reviews can no longer be submitted for this submission.',
                    'deadline_expired' => true
                ]);
            }
        }
        
        $setting = SubmissionSetting::where(['conference_id' => $conference->id, 'status' => 1])->first();

        // Get article type setting sections if available
        $articleTypeSections = null;
        if ($submission->articleType && $submission->articleType->setting) {
            $articleTypeSections = $submission->articleType->setting->sections;
        }

        return view('backend.participant.submission.review-modal', compact('submission', 'setting', 'conference', 'society', 'articleTypeSections'));
    }

    public function reviewSubmit(Request $request)
    {
        // dd($request->all());
        $submission = Submission::with('articleType.setting')->findOrFail($request->id);
        
        // Check if review deadline has passed
        if ($submission->review_deadline) {
            $deadline = \Carbon\Carbon::parse($submission->review_deadline);
            if (\Carbon\Carbon::now()->greaterThan($deadline)) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Review deadline has expired. Reviews can no longer be submitted for this submission.'
                ], 422);
            }
        } 
        
        $setting = SubmissionSetting::where(['conference_id' => $submission->conference_id, 'status' => 1])->first();

        // Determine if scoring is required or nullable (changed to numeric to support decimal values)
        $scoreRule = $setting->scoring_allowed == 1 ? 'required|numeric' : 'nullable|numeric';

        // Check if article type has sections for section-based rating
        $hasSectionRatings = $submission->articleType &&
            $submission->articleType->setting &&
            !empty($submission->articleType->setting->sections);

        $rules = [];

        // Build validation rules based on structure type
        if ($request->requestType == 1) {
            $rules['remarks'] = 'required';
            if ($request->has('sections') && is_array($request->sections)) {
                // Validate sections
                foreach ($request->sections as $index => $section) {
                    $rules["sections.{$index}.content"] = 'required|string';
                }
            } else {
                // Validate abstract content
                $rules['abstract_content'] = 'required';
            }

            // Validate ratings based on section-based or default structure
            if ($hasSectionRatings) {
                // Title rating validation (if title scoring is enabled)
                if ($submission->articleType->setting->title_scoring_enabled ?? false) {
                    $titleMaxMarks = $submission->articleType->setting->title_max_marks ?? 0;
                    $rules['title_rating'] = $scoreRule . "|min:0|max:{$titleMaxMarks}";
                }
                
                // Section-based ratings validation with dynamic max marks
                if ($request->has('section_ratings') && is_array($request->section_ratings)) {
                    $sections = $submission->articleType->setting->sections ?? [];
                    foreach ($request->section_ratings as $index => $rating) {
                        // Get max marks for this specific section
                        $sectionMaxMarks = $sections[$index]['max_marks'] ?? 2;
                        $rules["section_ratings.{$index}.rating"] = $scoreRule . "|min:0|max:{$sectionMaxMarks}";
                    }
                }

                // Get total marks from article type setting (default 10)
                $totalMarks = $submission->articleType->setting->total_marks ?? 10;
                
                // Calculate maximum possible score based on sum of section max_marks and title marks
                $maxPossibleScore = 0;
                
                // Add title marks if enabled
                if ($submission->articleType->setting->title_scoring_enabled ?? false) {
                    $maxPossibleScore += $submission->articleType->setting->title_max_marks ?? 0;
                }
                
                // Add section marks
                $sections = $submission->articleType->setting->sections ?? [];
                foreach ($sections as $section) {
                    $maxPossibleScore += $section['max_marks'] ?? 2;
                }

                // Overall rating is required only if maximum possible score < total marks
                if ($maxPossibleScore < $totalMarks) {
                    $remaining = $totalMarks - $maxPossibleScore;
                    $rules['overall_rating'] = "required|numeric|min:0|max:{$remaining}";
                }
            } else {
                // Default rating structure
                if ($request->structure) {
                    // Structured review: single overall rating
                    $rules['overall_rating'] = $scoreRule;
                } else {
                    // Detailed review: individual scores
                    $rules['introduction'] = $scoreRule;
                    $rules['method'] = $scoreRule;
                    $rules['result'] = $scoreRule;
                    $rules['conclusion'] = $scoreRule;
                    $rules['grammar'] = $scoreRule;
                }
            }
        } elseif ($request->requestType == 2) {
            // Rejection requires only reject remarks
            $rules['reject_remarks'] = 'required';
        }

        // Build custom validation messages with actual section names
        $customMessages = [];
        if ($hasSectionRatings && $request->has('section_ratings') && is_array($request->section_ratings)) {
            $sections = $submission->articleType->setting->sections ?? [];
            foreach ($request->section_ratings as $index => $rating) {
                $sectionName = $sections[$index]['name'] ?? 'Section ' . ($index + 1);
                $sectionMaxMarks = $sections[$index]['max_marks'] ?? 2;
                $customMessages["section_ratings.{$index}.rating.required"] = "The {$sectionName} rating is required.";
                $customMessages["section_ratings.{$index}.rating.numeric"] = "The {$sectionName} rating must be a number.";
                $customMessages["section_ratings.{$index}.rating.min"] = "The {$sectionName} rating must be at least 0.";
                $customMessages["section_ratings.{$index}.rating.max"] = "The {$sectionName} rating must not exceed {$sectionMaxMarks}.";
            }
        }

        // Add custom message for title rating if enabled
        if ($hasSectionRatings && ($submission->articleType->setting->title_scoring_enabled ?? false)) {
            $titleMaxMarks = $submission->articleType->setting->title_max_marks ?? 0;
            $customMessages['title_rating.required'] = 'The Title rating is required.';
            $customMessages['title_rating.numeric'] = 'The Title rating must be a number.';
            $customMessages['title_rating.min'] = 'The Title rating must be at least 0.';
            $customMessages['title_rating.max'] = "The Title rating must not exceed {$titleMaxMarks}.";
        }

        // Add custom message for overall rating if required
        if ($hasSectionRatings && isset($rules['overall_rating'])) {
            $totalMarks = $submission->articleType->setting->total_marks ?? 10;
            $maxPossibleScore = 0;
            if ($submission->articleType->setting->title_scoring_enabled ?? false) {
                $maxPossibleScore += $submission->articleType->setting->title_max_marks ?? 0;
            }
            $sections = $submission->articleType->setting->sections ?? [];
            foreach ($sections as $section) {
                $maxPossibleScore += $section['max_marks'] ?? 2;
            }
            $remaining = $totalMarks - $maxPossibleScore;
            $customMessages['overall_rating.required'] = 'The Overall Rating is required.';
            $customMessages['overall_rating.numeric'] = 'The Overall Rating must be a number.';
            $customMessages['overall_rating.min'] = 'The Overall Rating must be at least 0.';
            $customMessages['overall_rating.max'] = "The Overall Rating must not exceed {$remaining}.";
        }

        // Validate the request - this will automatically return 422 with errors if validation fails
        $validated = $request->validate($rules, $customMessages);
        
        try {


            // Manually add overall_rating to validated array if it exists (for section-based scoring)
            if ($hasSectionRatings && $request->has('overall_rating') && $request->overall_rating !== null && $request->overall_rating !== '') {
                $validated['overall_rating'] = $request->overall_rating;
            }

            if ($request->has('sections') && is_array($request->sections)) {
                $validated['sections'] = $request->sections;
                $validated['abstract_content'] = null; // Clear abstract content when using sections
            } else {
                $validated['sections'] = null; // Clear sections when using abstract content
            }
            DB::beginTransaction();

            // Update submission
            if ($request->requestType == 1) {
                $submission->update([
                    'review_status' => 1,
                    'sections' => $validated['sections'],
                    'abstract_content' => $validated['abstract_content'],
                ]);
            } elseif ($request->requestType == 2) {
                $submission->update([
                    'review_status' => 0,
                    'reject_remark' => $validated['reject_remarks'],
                ]);
            }

            // Prepare submission rating data
            if ($hasSectionRatings && $request->has('section_ratings')) {
                // Section-based rating
                $ratingData = [
                    'title_rating' => $request->title_rating ?? null,
                    'section_ratings' => $request->section_ratings,
                    'grammar' => $validated['grammar'] ?? null,
                    'overall_rating' => $validated['overall_rating'] ?? null, // Store overall rating if provided
                ];
            } else {
                // Default rating structure
                $ratingData = $request->structure
                    ? ['overall_rating' => $validated['overall_rating']]
                    : collect($validated)->only([
                        'grammar',
                        'conclusion',
                        'result',
                        'method',
                        'introduction'
                    ])->toArray();
            }

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
        $submission = Submission::with('submissionCategoryMajorTrack', 'presenter')->whereId($id)->first();

        if (!$submission) {
            return redirect()->back()->with('delete', 'Submission not found.');
        }

        // If no confirmation parameter, show the presentation change page
        if ($request->input('confirmation') === null) {
            return view('backend.participant.submission.presentation-type-change', compact('submission', 'conference', 'society'));
        }

        // Determine the new presentation type after confirmation
        if ($submission->presentation_type == 2) {
            $newValue = 1;
        } else {
            $newValue = 2;
        }

        // Process confirmation response
        if ($request->input('confirmation') == 'yes') {
            $presentation_type_change = 1;
            $message = 'Presentation type changed to ' . ($newValue == 1 ? 'Poster' : 'Oral') . ' successfully.';
        } elseif ($request->input('confirmation') == 'no') {
            $presentation_type_change = 2;
            $message = 'Presentation type change request declined.';
        } else {
            return redirect()->back()->with('delete', 'Invalid confirmation value.');
        }

        DB::beginTransaction();
        try {
            $submission->update([
                'presentation_type_change' => $presentation_type_change,
                'presentation_type' => $newValue
            ]);

            // Log activity
            logActivity(
                $conference->id,
                'Presentation Type Response',
                $submission->title . ' - Author ' . ($presentation_type_change == 1 ? 'accepted' : 'rejected') . 
                ' change from ' . ($submission->presentation_type == 1 ? 'Poster' : 'Oral') . ' to ' . 
                ($newValue == 1 ? 'Poster' : 'Oral')
            );

            DB::commit();
            return redirect()->route('my-society.conference.submission.index', [$society, $conference])->with('status', $message);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('delete', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function getArticleTypeSetting(Request $request, $society, $conference)
    {
        $articleType = ArticleType::with('setting')->find($request->article_type_id);

        if (!$articleType || !$articleType->setting) {
            return response()->json(['has_setting' => false]);
        }

        return response()->json([
            'has_setting' => true,
            'setting' => $articleType->setting
        ]);
    }

    public function convertArticleType(Request $request, $society, $conference, $id)
    {
        $submission = Submission::with('articleType', 'requestedArticleType', 'presenter')->whereId($id)->first();

        if (!$submission) {
            return redirect()->back()->with('delete', 'Submission not found.');
        }

        if ($submission->article_type_change !== 0) {
            return redirect()->route('my-society.conference.submission.index', [$society, $conference])
                ->with('delete', 'No pending category change request found for this submission.');
        }

        // Show the response page if no confirmation param
        if ($request->input('confirmation') === null) {
            return view('backend.participant.submission.article-type-change', compact('submission', 'conference', 'society'));
        }

        $confirmation = $request->input('confirmation');

        if ($confirmation === 'yes') {
            $article_type_change = 1;
            $message = 'Presentation category changed to "' . ($submission->requestedArticleType?->name ?? 'requested category') . '" successfully.';
        } elseif ($confirmation === 'no') {
            $article_type_change = 2;
            $message = 'Presentation category change request declined.';
        } else {
            return redirect()->back()->with('delete', 'Invalid confirmation value.');
        }

        DB::beginTransaction();
        try {
            $updateData = ['article_type_change' => $article_type_change];

            if ($confirmation === 'yes' && $submission->requested_article_type_id) {
                $updateData['article_type_id'] = $submission->requested_article_type_id;
            }

            $submission->update($updateData);

            logActivity(
                $conference->id,
                'Presentation Category Change Response',
                $submission->title . ' - Author ' . ($article_type_change == 1 ? 'accepted' : 'rejected') .
                ' category change from "' . ($submission->articleType?->name ?? 'N/A') . '"' .
                ($article_type_change == 1 ? ' to "' . ($submission->requestedArticleType?->name ?? 'N/A') . '"' : '')
            );

            DB::commit();
            return redirect()->route('my-society.conference.submission.index', [$society, $conference])
                ->with('status', $message);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('delete', 'Something went wrong: ' . $e->getMessage());
        }
    }
}
