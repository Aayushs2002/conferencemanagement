<?php

namespace App\Http\Controllers\Backend\Participant;

use App\Http\Controllers\Controller;
use App\Models\Conference\Author;
use App\Models\Conference\Contribution;
use App\Models\Conference\Submission;
use App\Models\SubmissionSetting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthorController extends Controller
{
    public function index($society, $conference, $submission)
    {
        $authors = Author::where([
            'submission_id' => $submission->id,
            'status' => 1
        ])->orderBy('id', 'asc')->get();

        return view('backend.participant.submission.author.index', compact('society', 'conference', 'submission', 'authors'));
    }

    public function create(Request $request, $society, $conference)
    {
        $submission = Submission::select('id', 'title')->where('id', $request->topicId)->first();
        $author = null;
        if ($request->has('authorId')) {
            $author = Author::where('id', $request->authorId)->first();
        }
        $authors = Author::where('submission_id', $request->topicId)->get();
        $authorLimit = SubmissionSetting::select('authors_limit')->first();

        $checkMainAuthor = Author::select('main_author')
            ->where('submission_id', $submission->id)
            ->get()
            ->pluck('main_author')
            ->toArray();

        // Get contributions if enabled
        $contributions = [];
        $contributionEnabled = false;
        $submissionSetting = SubmissionSetting::where('conference_id', $conference->id)->first();

        if ($submissionSetting && $submissionSetting->contribution_enabled) {
            $contributionEnabled = true;
            $contributions = Contribution::where([
                'conference_id' => $conference->id,
                'status' => 1
            ])->orderBy('name', 'asc')->get();
        }

        return view('backend.participant.submission.author.create', compact('society', 'submission', 'author', 'authors', 'authorLimit', 'checkMainAuthor', 'conference', 'contributions', 'contributionEnabled'));
    }

    public function oldAuthor(Request $request)
    {
        $author = Author::select('designation', 'institution', 'institution_address')->where('id', $request->oldAuthor)->first();
        return response()->json($author);
    }

    public function store(Request $request, $society, $conference)
    {
        try {
            // Setup validation rules
            $rules = [
                'submission_id' => 'required',
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('authors')->where(function ($query) use ($request) {
                        return $query->where('submission_id', $request->submission_id)
                            ->where('status', 1);
                    })
                ],
                'designation' => 'required|string|max:255',
                'institution' => 'required|string|max:255',
                'institution_address' => 'required|string|max:500',
                'main_author' => 'nullable',
            ];

            // Check if contributions are enabled
            // Check if contributions are enabled
            $submissionSetting = SubmissionSetting::where('conference_id', $conference->id)->first();
            if ($submissionSetting && $submissionSetting->contribution_enabled) {
                // Only require contributions if "other" checkbox is not selected
                if (!$request->has('contribution_other_checkbox')) {
                    $rules['contributions'] = 'required|array|min:1';
                    $rules['contributions.*'] = 'exists:contributions,id';
                } else {
                    $rules['contributions'] = 'nullable|array';
                    $rules['contributions.*'] = 'exists:contributions,id';
                }

                // If other checkbox is selected, require the text
                if ($request->has('contribution_other_checkbox')) {
                    $rules['contribution_other_text'] = 'required|string|max:255';
                } else {
                    $rules['contribution_other_text'] = 'nullable|string|max:255';
                }
            } else {
                $rules['contributions'] = 'nullable|array';
                $rules['contributions.*'] = 'exists:contributions,id';
                $rules['contribution_other_text'] = 'nullable|string|max:255';
            }

            if ($request->has('main_author') && $request->main_author == '1') {
                $rules['phone'] = [
                    'required',
                    'string',
                    'max:20',
                    Rule::unique('authors')->where(function ($query) use ($request) {
                        return $query->where('submission_id', $request->submission_id)
                            ->where('status', 1);
                    })
                ];
            } else {
                $rules['phone'] = [
                    'nullable',
                    'string',
                    'max:20',
                    Rule::unique('authors')->where(function ($query) use ($request) {
                        return $query->where('submission_id', $request->submission_id)
                            ->where('status', 1)
                            ->whereNotNull('phone');
                    })
                ];
            }

            // Custom validation messages
            $messages = [
                'name.required' => 'The author name is required.',
                'email.required' => 'The email address is required.',
                'email.email' => 'Please provide a valid email address.',
                'email.unique' => 'This email is already used by another author in this submission.',
                'designation.required' => 'The designation is required.',
                'institution.required' => 'The institution name is required.',
                'institution_address.required' => 'The institution address is required.',
                'phone.required' => 'Phone number is required for main author.',
                'phone.unique' => 'This phone number is already used by another author in this submission.',
                'contributions.required' => 'Please select at least one contribution.',
                'contributions.min' => 'Please select at least one contribution.',
            ];

            // Validate the request
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors()
                    ], 422);
                }
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $validated = $validator->validated();

            // Additional validation: Check if at least one contribution is provided when enabled
            // Additional validation: Check if at least one contribution is provided when enabled
            if ($submissionSetting && $submissionSetting->contribution_enabled) {
                $hasContributions = !empty($validated['contributions']) && count($validated['contributions']) > 0;
                $hasOtherContribution = $request->has('contribution_other_checkbox') && !empty($request->contribution_other_text);

                if (!$hasContributions && !$hasOtherContribution) {
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'errors' => ['contributions' => ['Please select at least one contribution or specify other.']]
                        ], 422);
                    }
                    return redirect()->back()->withErrors(['contributions' => 'Please select at least one contribution or specify other.'])->withInput();
                }
            }

            // Get the submission to check article type
            $submission = Submission::with('articleType.setting')->find($request->submission_id);
            $authorsCount = Author::where(['submission_id' => $request->submission_id, 'status' => 1])->count();

            // Check article type setting first (priority), then fallback to submission setting
            $authorLimitValue = null;
            if ($submission && $submission->articleType && $submission->articleType->setting && $submission->articleType->setting->author_limit) {
                $authorLimitValue = $submission->articleType->setting->author_limit;
            } else {
                $authorLimit = SubmissionSetting::where('conference_id', $conference->id)->select('authors_limit')->first();
                $authorLimitValue = $authorLimit->authors_limit ?? null;
            }

            if ($authorLimitValue && $authorLimitValue <= $authorsCount) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Author limit reached. Maximum ' . $authorLimitValue . ' authors allowed.'
                    ], 400);
                }
                return redirect()->back()->with('delete', 'Author Limit Reached.');
            }

            // Process main_author field
            $validated['main_author'] = $request->has('main_author') ? 1 : 0;

            // Handle contributions
            $contributions = $validated['contributions'] ?? [];
            unset($validated['contributions']);

            // Handle other contribution text
            $validated['contribution_other'] = $validated['contribution_other_text'] ?? null;
            unset($validated['contribution_other_text']);

            // Create the author
            $author = Author::create($validated);

            // Attach contributions if provided
            if (!empty($contributions)) {
                $author->contributions()->attach($contributions);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Author added successfully.',
                    'author' => $author->load('contributions')
                ], 200);
            }

            return redirect()->back()->with('status', 'Author Added Successfully');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while adding the author. Please try again.'
                ], 500);
            }
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    public function update(Request $request, $society, $conference, $author)
    {
        try {
            $author = Author::whereId($author->id)->first();

            if (!$author) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Author not found.'
                    ], 404);
                }
                return redirect()->back()->with('delete', 'Author not found.');
            }

            // Setup validation rules
            $rules = [
                'submission_id' => 'required',
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('authors')->where(function ($query) use ($request) {
                        return $query->where('submission_id', $request->submission_id)
                            ->where('status', 1);
                    })->ignore($author->id)
                ],
                'designation' => 'required|string|max:255',
                'institution' => 'required|string|max:255',
                'institution_address' => 'required|string|max:500',
                'main_author' => 'nullable',
            ];

            // Check if contributions are enabled
            // Check if contributions are enabled
            $submissionSetting = SubmissionSetting::where('conference_id', $conference->id)->first();
            if ($submissionSetting && $submissionSetting->contribution_enabled) {
                // Only require contributions if "other" checkbox is not selected
                if (!$request->has('contribution_other_checkbox')) {
                    $rules['contributions'] = 'nullable|array';
                    $rules['contributions.*'] = 'exists:contributions,id';
                } else {
                    $rules['contributions'] = 'nullable|array';
                    $rules['contributions.*'] = 'exists:contributions,id';
                }

                // If other checkbox is selected, require the text
                if ($request->has('contribution_other_checkbox')) {
                    $rules['contribution_other_text'] = 'required|string|max:255';
                } else {
                    $rules['contribution_other_text'] = 'nullable|string|max:255';
                }
            } else {
                $rules['contributions'] = 'nullable|array';
                $rules['contributions.*'] = 'exists:contributions,id';
                $rules['contribution_other_text'] = 'nullable|string|max:255';
            }

            if ($request->has('main_author') && $request->main_author == '1') {
                $rules['phone'] = [
                    'required',
                    'string',
                    'max:20',
                    Rule::unique('authors')->where(function ($query) use ($request) {
                        return $query->where('submission_id', $request->submission_id)
                            ->where('status', 1);
                    })->ignore($author->id)
                ];
            } else {
                $rules['phone'] = [
                    'nullable',
                    'string',
                    'max:20',
                    Rule::unique('authors')->where(function ($query) use ($request) {
                        return $query->where('submission_id', $request->submission_id)
                            ->where('status', 1)
                            ->whereNotNull('phone');
                    })->ignore($author->id)
                ];
            }

            // Custom validation messages
            $messages = [
                'name.required' => 'The author name is required.',
                'email.required' => 'The email address is required.',
                'email.email' => 'Please provide a valid email address.',
                'email.unique' => 'This email is already used by another author in this submission.',
                'designation.required' => 'The designation is required.',
                'institution.required' => 'The institution name is required.',
                'institution_address.required' => 'The institution address is required.',
                'phone.required' => 'Phone number is required for main author.',
                'phone.unique' => 'This phone number is already used by another author in this submission.',
                'contributions.required' => 'Please select at least one contribution.',
                'contributions.min' => 'Please select at least one contribution.',
            ];

            // Validate the request
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors()
                    ], 422);
                }
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $validated = $validator->validated();

            // Additional validation: Check if at least one contribution is provided when enabled
            if ($submissionSetting && $submissionSetting->contribution_enabled) {
                $hasContributions = !empty($validated['contributions']) && count($validated['contributions']) > 0;
                $hasOtherContribution = !empty($validated['contribution_other_text']);

                if (!$hasContributions && !$hasOtherContribution) {
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'errors' => ['contributions' => ['Please select at least one contribution or specify other.']]
                        ], 422);
                    }
                    return redirect()->back()->withErrors(['contributions' => 'Please select at least one contribution or specify other.'])->withInput();
                }
            }

            // Process main_author field
            $validated['main_author'] = $request->has('main_author') ? 1 : 0;

            // Handle contributions
            $contributions = $validated['contributions'] ?? [];
            unset($validated['contributions']);

            // Handle other contribution text
            $validated['contribution_other'] = $validated['contribution_other_text'] ?? null;
            unset($validated['contribution_other_text']);

            // Update the author
            $author->update($validated);

            // Sync contributions
            $author->contributions()->sync($contributions);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Author updated successfully.',
                    'author' => $author->load('contributions')
                ], 200);
            }

            return redirect()->back()->with('status', 'Author Updated Successfully');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the author. Please try again.'
                ], 500);
            }
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    public function destroy(Request $request, $society, $conference, $author)
    {
        try {
            $author = Author::whereId($author->id)->first();

            if (!$author) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Author not found.'
                    ], 404);
                }
                return redirect()->back()->with('delete', 'Author not found.');
            }

            $author->update(['status' => 0]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Author deleted successfully.'
                ], 200);
            }

            return redirect()->back()->with('status', 'Author Deleted Successfully');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting the author. Please try again.'
                ], 500);
            }
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }
}
