<?php

namespace App\Http\Controllers\Backend\Participant;

use App\Http\Controllers\Controller;
use App\Models\Conference\Author;
use App\Models\Conference\Submission;
use App\Models\SubmissionSetting;
use Exception;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index($society, $conference, $submission)
    {
        // dd($submission);
        $authors = Author::where([
            'submission_id' => $submission->id,
            'status' => 1
        ])->orderBy('id', 'asc')->get();

        // dd($authors);   
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

        return view('backend.participant.submission.author.create', compact('society', 'submission', 'author', 'authors', 'authorLimit', 'checkMainAuthor', 'conference'));
    }

    public function oldAuthor(Request $request)
    {
        $author = Author::select('designation', 'institution', 'institution_address')->where('id', $request->oldAuthor)->first();
        return response()->json($author);
    }

    public function store(Request $request, $society, $conference)
    {
        try {
            $rules = [
                'submission_id' => 'required',
                'name' => 'required',
                'email' => 'required|email',
                'designation' => 'required',
                'institution' => 'required',
                'institution_address' => 'required',
                'main_author' => 'nullable',
            ];

            if ($request->has('main_author')) {
                $rules['phone'] = 'required';
            } else {
                $rules['phone'] = 'nullable';
            }

            $validated = $request->validate($rules);

            $authorLimit = SubmissionSetting::where('conference_id', $conference->id)->select('authors_limit')->first();
            $authorsCount = Author::where(['submission_id' => $request->submission_id, 'status' => 1])->get()->count();

            if (@$authorLimit->authors_limit == $authorsCount) {
                return redirect()->back()->with('delete', 'Author Limit Reached.');
            } else {
                Author::create($validated);

                return redirect()->back()->with('status', 'Author Added Successfully');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    public function update(Request $request, $society, $conference, $author)
    {

        try {
            $author = Author::whereId($author->id)->first();
            $rules = [
                'submission_id' => 'required',
                'name' => 'required',
                'email' => 'required|email',
                'designation' => 'required',
                'institution' => 'required',
                'institution_address' => 'required',
                'main_author' => 'nullable',
            ];

            if ($request->has('main_author')) {
                $rules['phone'] = 'required';
            } else {
                $rules['phone'] = 'nullable';
            }

            $validated = $request->validate($rules);

            if ($request->has('main_author')) {
                $validated['main_author'] = 1;
            } else {
                $validated['main_author'] = 0;
            }
            $author->update($validated);

            return redirect()->back()->with('status', 'Author Updated Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    public function destroy($society, $conference, $author)
    {
        try {
            $author = Author::whereId($author->id)->first();
            $author->update(['status' => 0]);

            return redirect()->back()->with('status', 'Author Deleted Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }
}
