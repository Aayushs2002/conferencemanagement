<?php

namespace App\Http\Controllers\Backend\Participant;

use App\Http\Controllers\Controller;
use App\Models\Workshop\Workshop;
use App\Models\Workshop\WorkshopRegistration;
use Illuminate\Http\Request;

class MyWorkshopRegistrationController extends Controller
{
    /**
     * Display a listing of registrations for participant's approved workshop
     */
    public function index(Request $request, $society, $conference, $workshop)
    {
        // Verify ownership and approval status
        if ($workshop->created_by != current_user()->id || $workshop->approval_status != 'approved') {
            abort(403, 'Unauthorized access to this workshop.');
        }

        $query = WorkshopRegistration::where([
            'workshop_id' => $workshop->id,
            'registrant_type' => 1,
            'status' => 1
        ]);

        if ($request->filled('meal_type')) {
            $query->where('meal_type', $request->meal_type);
        }

        $registrations = $query->latest()->get();

        return view('backend.participant.my-workshop-registration.index', compact('registrations', 'workshop', 'society', 'conference'));
    }

    /**
     * View registration details
     */
    public function view($society, $conference, Request $request)
    {
        $registrant = WorkshopRegistration::where('id', $request->id)->first();
        
        if (!$registrant) {
            return response()->json(['error' => 'Registration not found'], 404);
        }

        // Verify workshop ownership
        $workshop = Workshop::find($registrant->workshop_id);
        if (!$workshop || $workshop->created_by != current_user()->id || $workshop->approval_status != 'approved') {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        return view('backend.participant.my-workshop-registration.view', compact('registrant'));
    }

    /**
     * Show verify form
     */
    public function verifyForm($society, $conference, Request $request)
    {
        $registration = WorkshopRegistration::where('id', $request->id)->first();
        
        if (!$registration) {
            return response()->json(['error' => 'Registration not found'], 404);
        }

        // Verify workshop ownership
        $workshop = Workshop::find($registration->workshop_id);
        if (!$workshop || $workshop->created_by != current_user()->id || $workshop->approval_status != 'approved') {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        return view('backend.participant.my-workshop-registration.verify-registrant', compact('registration', 'society', 'conference'));
    }

    /**
     * Verify or reject registration
     */
    public function verify($society, $conference, Request $request)
    {
        try {
            $rules = [
                'verified_status' => 'required',
            ];

            if ($request->verified_status == 2) {
                $rules['remarks'] = 'required';
            }

            $validated = $request->validate($rules);

            $workshopRegistration = WorkshopRegistration::whereId($request->id)->first();
            
            if (!$workshopRegistration) {
                return response()->json(['type' => 'error', 'message' => 'Registration not found'], 404);
            }

            // Verify workshop ownership
            $workshop = Workshop::find($workshopRegistration->workshop_id);
            if (!$workshop || $workshop->created_by != current_user()->id || $workshop->approval_status != 'approved') {
                return response()->json(['type' => 'error', 'message' => 'Unauthorized access'], 403);
            }

            $workshopRegistration->update($validated);

            $message = $request->verified_status == 1 
                ? 'Registrant Accepted Successfully.' 
                : 'Registrant Rejected Successfully.';

            return response()->json(['type' => 'success', 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['type' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
