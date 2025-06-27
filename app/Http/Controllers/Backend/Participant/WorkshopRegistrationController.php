<?php

namespace App\Http\Controllers\Backend\Participant;

use App\Http\Controllers\Controller;
use App\Models\Conference\ConferenceMemberTypePrice;
use App\Models\User\UserSociety;
use App\Models\Workshop\Workshop;
use App\Models\Workshop\WorkshopRegistration;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WorkshopRegistrationController extends Controller
{
    public function index($society, $conference)
    {
        // dd(current_user()->id);
        $checkPayment = null;

        $workshops = Workshop::where([
            'conference_id' => $conference->id,
            'status' => 1
        ])->get();

        $societyUser = current_user()->societies->where('id', $conference->society_id)->first();

        $registrations = WorkshopRegistration::where([
            'user_id' => current_user()->id,
            'status' => 1
        ])->whereIn('workshop_id', $workshops->pluck('id'))->get();

        return view('backend.participant.workshop-registration.index', compact(
            'society',
            'workshops',
            'conference',
            'societyUser',
            'checkPayment',
            'registrations'
        ));
    }


    public function submitData(Request $request, $society, $conference)
    {
        try {
            $rules = [
                'workshop_id' => 'required',
                'transaction_id' => 'required|unique:workshop_registrations,transaction_id',
                'payment_type' => 'required',
                'amount' => 'required'
            ];

            $validated = $request->validate($rules);
            // $validated['payment_voucher'] = 'Fone-Pay';
            $validated['user_id'] = current_user()->id;
            $validated['token'] = random_word(60);
            $validated['verified_status'] = 1;
            // $workshop = Workshop::whereId($validated['workshop_id'])->first();
            // $mailData = [
            //     'receiverName' => $workshop->organizer->name,
            //     'workshopTitle' => $workshop->title,
            //     'senderName' => auth()->user()->name,
            // ];

            // Mail::to($workshop->contact_person_email)->send(new RegistrationMail($mailData));

            WorkshopRegistration::create($validated);

            return redirect()->route('my-society.conference.workshop.index', [$society, $conference])->with('status', 'Successfully registered for workshop.');
        } catch (Exception $e) {
            dd($e);
            return redirect()->back()->with('delete', 'Error while registering for workshop.');
        }
    }

    public function meal(Request $request, $society, $conference)
    {
        $registrant = WorkshopRegistration::whereId($request->id)->first();
        return view('backend.participant.workshop-registration.meal', compact('registrant', 'society', 'conference'));
    }


    public function submitMealPreference(Request $request, $society, $conference)
    {
        try {
            $validated = $request->validate([
                'meal_type' => 'required|in:1,2',
            ]);

            $registrant = WorkshopRegistration::whereId($request->id)->first();
            $registrant->update($validated);
            return response()->json(['message' => 'Meal preference submitted successfully!'], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
}
