<?php

namespace App\Http\Controllers\Backend\Conference;

use App\Http\Controllers\Controller;
use App\Models\Accomodation\Hotel;
use App\Models\Accomodation\InternationalAccommodation;
use App\Models\Conference\Conference;
use App\Models\Conference\ConferenceRegistration;
use App\Models\User;
use App\Models\User\Country;
use App\Models\User\Society;
use App\Notifications\AccommodationDetailReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InternationalAccommodationExport;

class AccommodationManagementController extends Controller
{
    public function index(Society $society, Conference $conference)
    {
        // All accommodation records for this conference
        $accommodations = InternationalAccommodation::query()
            ->whereHas('conferenceRegistration', function ($query) use ($conference) {
                $query->where('conference_id', $conference->id)
                    ->where('verified_status', 1);
            })
            ->with([
                'conferenceRegistration.user.userDetail.country',
                'hotel'
            ])
            ->get();

        $countries = Country::orderBy('country_name')->get();
        $hotels = Hotel::where(['conference_id' => $conference->id, 'status' => 1])->orderBy('name')->get();

        // Invited participants who accepted but need admin to fill accommodation
        $invitedAwaitingAccommodation = ConferenceRegistration::getInvitedAwaitingAccommodation($conference);
        // @dd($invitedAwaitingAccommodation);
        // Self-registered participants who need to fill their own accommodation
        $selfRegisteredNeedingAccommodation = ConferenceRegistration::getSelfRegisteredNeedingAccommodation($conference);

        return view('backend.conference.accommodation.index', compact(
            'society',
            'conference',
            'accommodations',
            'countries',
            'hotels',
            'invitedAwaitingAccommodation',
            'selfRegisteredNeedingAccommodation'
        ));
    }

    public function show(Society $society, Conference $conference, InternationalAccommodation $accommodation)
    {
        $accommodation->load([
            'conferenceRegistration.user.userDetail',
            'hotel'
        ]);

        return view('backend.conference.accommodation.show', compact('society', 'conference', 'accommodation'));
    }

    /**
     * Send accommodation reminder to a specific user
     */
    public function sendReminder(Request $request, Society $society, Conference $conference)
    {
        try {
            $user = User::findOrFail($request->user_id);

            $registration = ConferenceRegistration::where([
                'user_id' => $user->id,
                'conference_id' => $conference->id
            ])->where('verified_status', ConferenceRegistration::STATUS_ACCEPTED)
                ->first();

            if (!$registration) {
                throw new \Exception('User is not eligible for accommodation reminder.');
            }

            // Check if user can fill own accommodation (self-registered) or needs admin help (invited)
            if ($registration->canFillOwnAccommodation()) {
                // Send reminder to self-registered user to fill their own accommodation
                Notification::send($user, new AccommodationDetailReminder($conference));
                $message = 'Accommodation reminder sent to ' . $user->f_name . ' ' . $user->l_name . ' to fill their own details.';
            } elseif ($registration->canReceiveAdminAccommodation()) {
                // For invited users, this is a note for admin to fill accommodation
                $message = 'Note: ' . $user->f_name . ' ' . $user->l_name . ' is an invited participant. Admin should fill accommodation details.';
            } else {
                throw new \Exception('User is not eligible for accommodation assistance.');
            }

            return response()->json([
                'type' => 'success',
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Show form to create accommodation for invited participant
     */
    public function createForInvited(Request $request, Society $society, Conference $conference)
    {
        $user = User::with('userDetail.country')->findOrFail($request->user_id);

        $registration = ConferenceRegistration::where([
            'user_id' => $user->id,
            'conference_id' => $conference->id,
            'is_invited' => true
        ])->whereNotNull('invitation_accepted_at')
            ->where('verified_status', ConferenceRegistration::STATUS_ACCEPTED)
            ->first();

        if (!$registration || !$registration->canReceiveAdminAccommodation()) {
            return response()->json([
                'type' => 'error',
                'message' => 'User is not eligible for admin accommodation setup.'
            ], 400);
        }

        $hotels = Hotel::orderBy('name')->get();

        return view('backend.conference.accommodation.create-for-invited', compact(
            'user',
            'registration',
            'society',
            'conference',
            'hotels'
        ));
    }

    /**
     * Store accommodation details for invited participant
     */
    public function storeForInvited(Request $request, Society $society, Conference $conference)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'hotel_id' => 'required|exists:hotels,id',
                'arrival_date' => 'required|date',
                'arrival_time' => 'required',
                'departure_date' => 'required|date|after_or_equal:arrival_date',
                'departure_time' => 'required',
                'check_in_date' => 'required|date|after_or_equal:arrival_date',
                'check_out_date' => 'required|date|after_or_equal:check_in_date|before_or_equal:departure_date',
                'flight_number' => 'nullable|string|max:50',
                // 'departure_flight_number' => 'nullable|string|max:50',
                // 'special_requirements' => 'nullable|string|max:500',
                // 'airport_pickup_required' => 'boolean',
                // 'remarks' => 'nullable|string|max:500'
            ]);

            $registration = ConferenceRegistration::where([
                'user_id' => $validated['user_id'],
                'conference_id' => $conference->id,
                'is_invited' => true
            ])->whereNotNull('invitation_accepted_at')
                ->where('verified_status', ConferenceRegistration::STATUS_ACCEPTED)
                ->first();

            if (!$registration || !$registration->canReceiveAdminAccommodation()) {
                throw new \Exception('User is not eligible for admin accommodation setup.');
            }

            // Check if accommodation already exists
            if ($registration->internationalAccommodation) {
                throw new \Exception('Accommodation details already exist for this participant.');
            }

            DB::beginTransaction();

            // Create accommodation record
            InternationalAccommodation::create([
                'conference_registration_id' => $registration->id,
                'user_id' => $registration->user_id,
                'hotel_id' => $validated['hotel_id'],
                'arrival_date' => $validated['arrival_date'],
                'arrival_time' => $validated['arrival_time'],
                'departure_date' => $validated['departure_date'],
                'departure_time' => $validated['departure_time'],
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'flight_number' => $validated['flight_number'],
                // 'departure_flight_number' => $validated['departure_flight_number'],
                // 'special_requirements' => $validated['special_requirements'],
                // 'airport_pickup_required' => $validated['airport_pickup_required'] ?? false,
                // 'remarks' => $validated['remarks'],
                'created_by_admin' => true
            ]);

            // Log activity
            logActivity(
                $conference->id,
                'Admin Created Accommodation',
                'Accommodation details created by admin for ' . $registration->user->f_name . ' ' . $registration->user->l_name
            );

            DB::commit();

            return response()->json([
                'type' => 'success',
                'message' => 'Accommodation details created successfully for invited participant.'
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
     * Export accommodation data to Excel
     */
    public function export(Society $society, Conference $conference)
    {
        $accommodations = InternationalAccommodation::query()
            ->whereHas('conferenceRegistration', function ($query) use ($conference) {
                $query->where('conference_id', $conference->id)
                    ->where('verified_status', 1);
            })
            ->with([
                'conferenceRegistration.user.userDetail.country',
                'hotel'
            ])
            ->get();

        return Excel::download(
            new InternationalAccommodationExport($accommodations, $conference),
            'international-accommodations-' . $conference->slug . '-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
