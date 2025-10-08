<?php

namespace App\Http\Controllers\Frontend\Conference;

use App\Http\Controllers\Controller;
use App\Models\Notice\Notice;
use Illuminate\Http\Request;

class NewsAndNoticeController extends BaseConferenceController
{
    public function index()
    {
        $notices = Notice::where(['conference_id' => $this->conference->id, 'status' => 1])->paginate(6);
        return view('frontend.conference.news-and-notice.index', compact('notices'));
    }
}
