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
            ->select('abstract_word_limit', 'key_word_limit', 'deadline', 'attachment_name', 'attachment_required', 'abstract_guidelines', 'competition_enabled', 'contribution_enabled', 'copy_paste_allowed')
            ->first();
        // dd($setting);
        if (!$setting) {
            return redirect()->back()->with('delete', 'Submission settings not found.');
        }
        if (is_past($setting->deadline)) {
            return redirect()->back()->with('delete', 'Submission date has ended.');
        }
        $submissionTracks = SubmissionCategoryMajorTrack::where(['conference_id' => $conference->id, 'status' => 1])->get();
        $articleTypes = ArticleType::with('setting')->where(['conference_id' => $conference->id, 'status' => 1])->orderBy('display_order', 'asc')->orderBy('id', 'asc')->get();

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
 
        return view('backend.participant.submission.create', compact('society', 'conference', 'submissionTracks', 'setting', 'articleTypes', 'contributions', 'contributionEnabled'));
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
            $authUser = User::whereId(current_user()->id)->first();
            $validated['user_id'] = current_user()->id;
            $validated['conference_id'] = $conference->id;
            $validated['submitted_date'] = now();
            $validated['main_author'] = $validated['main_author'] ?? 0;

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

            // Validation: At least one author must be main author
            if ($validated['main_author'] == 0 && !$coAuthorIsMain) {
                return redirect()->back()->withInput()->with('delete', 'At least one author must be designated as the main author/presenter.');
            }

            // Validation: Only one author can be main author
            if ($validated['main_author'] == 1 && $coAuthorIsMain) {
                return redirect()->back()->withInput()->with('delete', 'Only one author can be the main author/presenter.');
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

            $template = EmailTemplate::where(['conference_id' => $conference->id, 'key' => 1])->first();

            $userMailData = [
                'name' => $authUser->fullName($authUser),
                'namePrefix' => $authUser->userDetail->namePrefix->prefix,
                'topic' => $validated['title'],
                'conferenceTheme' => $conference->conference_theme,
                'societyEmail' => $society->users->where('type', 2)->value('email'),
                'societyName' => $society->abbreviation,
                'conferenceDate' => $conferenceDate,
                'conferenceName' => $conference->conference_name
            ];

            $data = [
                'submission_topic' => $validated['title'],
                'conference_theme' => $conference->conference_theme,
                'conference_date' => $conferenceDate,
                'society_email' => $society->users->where('type', 2)->value('email'),
            ];

            $subject = parseTemplate($template?->subject, $data);
            $body = parseTemplate($template?->body, $data);
            Mail::to($authUser->email)->send(new SubmissionSubmittedToUserMail($userMailData, $subject, $body, $conference->conference_name));
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
                    // Set main_author based on checkbox value
                    $authorData['main_author'] = isset($authorData['main_author']) && $authorData['main_author'] == 1 ? 1 : 0;
                    
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

    public function edit($society, $conference, $submission)
    {
        // dd($submission);
        $submission->load(['authors.contributions']);
        $setting = SubmissionSetting::where('conference_id', $conference->id)
            ->select('abstract_word_limit', 'key_word_limit', 'deadline', 'attachment_name', 'attachment_required', 'abstract_guidelines', 'competition_enabled', 'contribution_enabled', 'copy_paste_allowed')
            ->first();
        if (!$setting) {
            return redirect()->back()->with('delete', 'Submission settings not found.');
        }
        if (is_past($setting->deadline)) {
            return redirect()->back()->with('delete', 'Submission date has ended.');
        }
        $submissionTracks = SubmissionCategoryMajorTrack::where(['conference_id' => $conference->id, 'status' => 1])->get();
        $articleTypes = ArticleType::with('setting')->where(['conference_id' => $conference->id, 'status' => 1])->orderBy('display_order', 'asc')->orderBy('id', 'asc')->get();

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
        return view('backend.participant.submission.create', compact('society', 'conference', 'submissionTracks', 'setting', 'submission', 'articleTypes', 'contributions', 'contributionEnabled'));
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
        try {
            $submission = Submission::with('articleType.setting')->findOrFail($request->id);
            $setting = SubmissionSetting::where(['conference_id' => $submission->conference_id, 'status' => 1])->first();

            // Determine if scoring is required or nullable
            $scoreRule = $setting->scoring_allowed == 1 ? 'required|integer' : 'nullable|integer';

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
                    // Section-based ratings validation
                    if ($request->has('section_ratings') && is_array($request->section_ratings)) {
                        foreach ($request->section_ratings as $index => $rating) {
                            $rules["section_ratings.{$index}.rating"] = $scoreRule;
                        }
                    }
                    $rules['grammar'] = $scoreRule; // Grammar is always required for section-based
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

            $validated = $request->validate($rules);
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
                    'section_ratings' => $request->section_ratings,
                    'grammar' => $validated['grammar'] ?? null,
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
}
