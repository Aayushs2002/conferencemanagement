<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Conference\ConferenceRegistration;
use App\Notifications\AccommodationDetailReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class InvitationController extends Controller
{
    /**
     * Show invitation acceptance form
     */
    public function show(Request $request, $token)
    {
        $registration = ConferenceRegistration::with(['user', 'conference'])
            ->where('invitation_response_token', $token)
            ->where('is_invited', true)
            ->whereNull('invitation_accepted_at')
            ->first();

        if (!$registration) {
            return view('frontend.invitation.expired', [
                'message' => 'This invitation link is invalid or has already been used.'
            ]);
        }

        return view('frontend.invitation.accept', compact('registration'));
    }

    /**
     * Process invitation acceptance
     */
    public function accept(Request $request, $token)
    {
        try {
            DB::beginTransaction();

            $registration = ConferenceRegistration::with(['user.userDetail', 'conference'])
                ->where('invitation_response_token', $token)
                ->where('is_invited', true)
                ->whereNull('invitation_accepted_at')
                ->lockForUpdate()
                ->first();

            if (!$registration) {
                throw new \Exception('Invalid or expired invitation link.');
            }

            // Accept the invitation
            $registration->acceptInvitation();

            // Send accommodation reminder if eligible
            // if ($registration->needsAccommodationReminder()) {
            //     Notification::send(
            //         $registration->user, 
            //         new AccommodationDetailReminder($registration->conference)
            //     );
            // }

            DB::commit();

            return response()->json([
                'type' => 'success',
                'message' => 'Invitation accepted successfully!',
                'redirect' => route('my-society.conference.index', [
                    $registration->conference->society, 
                    $registration->conference
                ])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'type' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Decline invitation
     */
    public function decline(Request $request, $token)
    {
        try {
            $registration = ConferenceRegistration::where('invitation_response_token', $token)
                ->where('is_invited', true)
                ->whereNull('invitation_accepted_at')
                ->first();

            if (!$registration) {
                throw new \Exception('Invalid or expired invitation link.');
            }

            $registration->update([
                'verified_status' => ConferenceRegistration::STATUS_REJECTED,
                'remarks' => 'Invitation declined by participant'
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'Invitation declined successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}