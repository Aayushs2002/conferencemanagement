<?php

namespace App\Http\Controllers\Backend\Submission;

use App\Http\Controllers\Controller;
use App\Models\Conference\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index($society, $conference, $submission)
    {
        $authors = Author::where(['submission_id' => $submission->id, 'status' => 1])->get();
        return view('backend.submission.author.index', compact('submission', 'authors', 'society', 'conference'));
    }
}
