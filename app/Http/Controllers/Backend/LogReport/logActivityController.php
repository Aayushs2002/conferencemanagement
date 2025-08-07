<?php

namespace App\Http\Controllers\Backend\LogReport;

use App\Http\Controllers\Controller;
use App\Models\User\ActivityLog;
use Illuminate\Http\Request;

class logActivityController extends Controller
{
    public function index($society, $conference)
    {
        $activityLogs = ActivityLog::where('user_id', '!=', 1)
            ->where('conference_id', $conference->id)
            ->latest()
            ->get();

        return view('backend.log-report.log-activity.index', compact('activityLogs'));
    }
}
