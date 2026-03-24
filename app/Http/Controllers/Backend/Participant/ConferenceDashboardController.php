<?php

namespace App\Http\Controllers\Backend\Participant;

use App\Http\Controllers\Controller;
use App\Models\Conference\ConferenceRegistration;
use App\Models\Conference\Submission;
use App\Models\Workshop\Workshop;
use App\Models\Workshop\WorkshopRegistration;
use Illuminate\Http\Request;

class ConferenceDashboardController extends Controller
{
    public function index($conference)
    {
        $submissionCount = Submission::where(['conference_id' => $conference->id, 'user_id' => current_user()->id, 'status' => 1])->count();
        $workshop = Workshop::where(['conference_id' => $conference->id, 'status' => 1])->pluck('id');
        $workshopRegistrationCount = WorkshopRegistration::where(['user_id' => current_user()->id, 'status' => 1])->whereIn('workshop_id', $workshop)->count();
        
        // Get pending presentation type change requests
        $pendingPresentationTypeRequests = Submission::where([
            'conference_id' => $conference->id,
            'user_id' => current_user()->id,
            'status' => 1,
            'presentation_type_change' => 0  // 0 = sent to author, awaiting response
        ])->get();

        return view('backend.participant.conference.conference-dashboard', compact('conference', 'submissionCount', 'workshopRegistrationCount', 'pendingPresentationTypeRequests'));
    }
}
