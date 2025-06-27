<?php

namespace App\Http\Controllers\Backend\Participant;

use App\Http\Controllers\Controller;
use App\Models\Conference\AccompanyPerson;
use App\Models\Conference\ConferenceMemberTypePrice;
use App\Models\Conference\ConferenceRegistration;
use App\Models\Conference\Submission;
use App\Models\Payment\NationalPayment;
use App\Models\User\UserSociety;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConferenceRegistrationController extends Controller
{
    public function index($society, $conference)
    {
        $registrations = ConferenceRegistration::where(['user_id' => current_user()->id, 'status' => 1, 'conference_id' => $conference->id])->get();
        $conference_registration = ConferenceRegistration::where(['user_id' => current_user()->id, 'conference_id' => $conference->id, 'status' => 1])->first();
        return view('backend.participant.conference-registration.index', compact('conference', 'society', 'registrations', 'conference_registration'));
    }

    public function create($society, $conference)
    {
        $membetType = current_user()->societies->where('id', $conference->society_id)->first()?->pivot?->memberType;
        $memberTypePrice = ConferenceMemberTypePrice::where(['conference_id' => $conference->id, 'member_type_id' => $membetType->id])->first();
        $amount = '';
        if (!empty($conference)) {
            if ($conference->early_bird_registration_deadline >= date('Y-m-d')) {
                $amount = !empty($memberTypePrice->early_bird_amount) ? $memberTypePrice->early_bird_amount : '';
            } elseif ($conference->regular_registration_deadline >= date('Y-m-d')) {
                $amount = !empty($memberTypePrice->regular_amount) ? $memberTypePrice->regular_amount : '';
            }
        }
        $national_payemnt_setting = NationalPayment::where('society_id', $conference->society_id)->first();

        return view('backend.participant.conference-registration.create', compact('conference', 'amount', 'memberTypePrice', 'society', 'national_payemnt_setting'));
    }

    public function checkSubmission(Request $request, $society, $conference)
    {
        // dd($conference);
        $checkSubmission = Submission::where(['user_id' => current_user()->id, 'conference_id' => $conference->id, 'status' => 1])->first();

        if (empty($checkSubmission)) {
            $checkSubmissionValue = 'not-submitted';
        } else {
            $checkSubmissionValue = 'submitted';
        }
        // $checkSubmissionValue = 'not-submitted';
        return response()->json(['checkSubmission' => $checkSubmissionValue]);
    }

    public function onlinePaymentSubmit(Request $request, $society, $conference)
    {
        try {

            if (is_past($conference->regular_registration_deadline)) {
                return redirect()->back()->with('delete', 'Registration deadline has been ended.');
            } else {
                $checkDuplicateRegistration = ConferenceRegistration::where(['user_id' => current_user()->id, 'conference_id' => $conference->id, 'status' => 1])->first();
                if (empty($checkDuplicateRegistration)) {
                    $rules = [
                        'accompany_person' => 'nullable|numeric',
                        'registrant_type' => 'required',
                        'amount' => 'required',
                        'payment_type' => 'required',
                        'transaction_id' => 'required|unique:conference_registrations,transaction_id'
                    ];

                    $message = [
                        'transaction_id.unique' => 'Transaction/Reference Id already exist.',
                        'person_name.*.required' => 'Each person name is required.',
                    ];

                    $validated = $request->validate($rules, $message);

                    $authUser = current_user();
                    $validated['user_id'] = $authUser->id;
                    $validated['verified_status'] = 1;
                    $validated['conference_id'] = $conference->id;
                    $validated['total_attendee'] = empty($request->accompany_person) ? 1 : $request->accompany_person + 1;
                    $validated['token'] = random_word(60);

                    // $mailData = [
                    //     'conference_theme' => $conference->conference_theme,
                    //     'name' => $authUser->fullName($authUser),
                    //     'namePrefix' => $authUser->namePrefix->prefix ?? null,
                    //     'email' => $authUser->email,
                    // ];

                    // Mail::to($authUser->email)->send(new RegistrationByUserMail($mailData));

                    DB::beginTransaction();

                    ConferenceRegistration::create($validated);


                    DB::commit();
                    request()->session()->forget('onlinePayment');
                    return redirect()->route('my-society.conference.index', [$society, $conference])->with('status', 'Successfully registered to conference.');
                } else {
                    return redirect()->back()->with('delete', 'Registration already done for current conference.');
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateRegistration(Request $request, $society, $conference)
    {
        $registration = ConferenceRegistration::where(['user_id' => current_user()->id, 'conference_id' => $conference->id, 'status' => 1])->first();
        $rules = [
            'meal_type' => 'required',
        ];

        if ($registration->total_attendee > 1) {
            $rules['person_name.*'] = 'required';
        }

        if ($registration->registrant_type == 2) {
            $rules['short_cv'] = 'required';
        }
        $message = [
            'person_name.*.required' => 'Each person name is required.',
        ];
        $validated = $request->validate($rules, $message);
        try {

            DB::beginTransaction();

            $registration->update($validated);

            if ($registration->total_attendee > 1) {
                $insertArray = [];
                foreach ($validated['person_name'] as $key => $value) {
                    $array['conference_registration_id'] = $registration->id;
                    $array['person_name'] = $value;
                    $array['created_at'] = now();
                    $array['updated_at'] = now();
                    $insertArray[] = $array;
                }
                AccompanyPerson::insert($insertArray);
            }
            DB::commit();
            return redirect()->back()->withInput()->with('status', 'Successfully registered conference updated.');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            // throw $e;
            return redirect()->back()->with('delete', 'Filed are required.');
        }
    }
}
