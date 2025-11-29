<?php

namespace App\Http\Controllers\Backend\Participant;

use App\Http\Controllers\Controller;
use App\Mail\Workshop\Registration\UserRegistrationMail;
use App\Models\Conference\ConferenceMemberTypePrice;
use App\Models\Conference\ConferenceSetting;
use App\Models\Payment\InternationalPayment;
use App\Models\Payment\NationalPayment;
use App\Models\User\UserSociety;
use App\Models\Workshop\Workshop;
use App\Models\Workshop\WorkshopRegistration;
use App\Services\File\FileService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class WorkshopRegistrationController extends Controller
{ 

    public function __construct(protected FileService $file_service) {}

    public function index($society, $conference)
    {
        $checkPayment = null;

        $workshops = Workshop::where([
            'conference_id' => $conference->id,
            'approval_status' => 'approved',
            'status' => 1
        ])->get();
        // dd($workshops);
        $societyUser = current_user()->societies->where('id', $conference->society_id)->first();

        $registrations = WorkshopRegistration::where([
            'user_id' => current_user()->id,
            'status' => 1
        ])->whereIn('workshop_id', $workshops->pluck('id'))->get();
        $national_payemnt_setting = NationalPayment::where('society_id', $conference->society_id)->first();
        $international_payemnt_setting = InternationalPayment::where('society_id', $conference->society_id)->first();
        return view('backend.participant.workshop-registration.index', compact(
            'society',
            'workshops',
            'conference',
            'societyUser',
            'checkPayment',
            'registrations',
            'national_payemnt_setting',
            'international_payemnt_setting'
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
            $authUser = current_user();
            $validated['user_id'] = current_user()->id;
            $validated['token'] = random_word(60);
            $validated['verified_status'] = 1;
            $date = \Carbon\Carbon::now()->format('F j, Y');

            $paymentTypes = [
                1 => 'FonePay',
                2 => 'Moco',
                3 => 'Esewa',
                4 => 'Khalti',
                5 => 'Card Payment'
            ];
            $paymentType = $paymentTypes[$request->payment_type] ?? 'Unknown';
            $workshop = Workshop::whereId($validated['workshop_id'])->first();
            $conferenceSetting = ConferenceSetting::where('conference_id', $conference->id)->first();

            $workshopData = null;
            $workshopData[] = [
                'name'   => $workshop->workshop_title ?? 'Workshop',
                'amount' => $validated['amount']
            ];
            $mailData = [
                'conference_theme' => $conference->conference_theme,
                'conference_name'  => $conference->conference_name,
                'name'             => $authUser->fullName($authUser),
                'namePrefix'       => $authUser->userDetail->namePrefix->prefix,
                'email'            => $authUser->email,
                'paymentType'      => $paymentType,
                'transactionId'    => $validated['transaction_id'],
                'amount'           => $validated['amount'],
                'amountInWord'     => numberToWord($validated['amount']),
                'date'             => $date,
                'societyName'      => $society->users->where('type', 2)->first()->f_name,
                'societyLogo'      => $society->logo,
                'societyPhone'     => $society->phone,
                'societyEmail'     => $society->users->where('type', 2)->first()->email,
                'societyAddress'   => $society->address,
                'primaryColor'     => $conference->primary_color,
                'country'          => $authUser->userDetail->country_id,
                'signatureName'    => $conferenceSetting->name,
                'signature'        => $conferenceSetting->signature,
                'conferenceAmount' => null,
                'addons'           => [],
                'workshop'         => $workshopData,
                'accompany' => null
            ];

            Mail::to($authUser->email)->send(new UserRegistrationMail($mailData, $conference->conference_name));

            WorkshopRegistration::create($validated);

            return redirect()->route('my-society.conference.workshop.index', [$society, $conference])->with('status', 'Successfully registered for workshop.');
        } catch (Exception $e) {
            return redirect()->back()->with('delete', 'Error while registering for workshop.');
        }
    }

    public function store(Request $request, $society, $conference, $workshop)
    {
        $rules = [
            'transaction_id' => 'required|unique:workshop_registrations,transaction_id',
            'payment_voucher' => 'required',
            'price' => 'required'
        ];

        $validated = $request->validate($rules);
        try {
            $rules = [
                'transaction_id' => 'required|unique:workshop_registrations,transaction_id',
                'payment_voucher' => 'required',
                'price' => 'required'
            ];
            $validated = $request->validate($rules);
            $authUser = current_user();
            $validated['payment_type'] = 6;
            $validated['user_id'] = current_user()->id;
            $validated['token'] = random_word(60);
            $validated['verified_status'] = 0;
            $validated['workshop_id'] = $workshop->id;
            $validated['amount'] = $request->price;
            $date = \Carbon\Carbon::now()->format('F j, Y');
            $conferenceSetting = ConferenceSetting::where('conference_id', $conference->id)->first();
            $workshopData = null;
            $workshopData[] = [
                'name'   => $workshop->workshop_title ?? 'Workshop',
                'amount' => $validated['amount']
            ];

            $mailData = [
                'conference_theme' => $conference->conference_theme,
                'conference_name'  => $conference->conference_name,
                'name'             => $authUser->fullName($authUser),
                'namePrefix'       => $authUser->userDetail->namePrefix->prefix,
                'email'            => $authUser->email,
                'paymentType'      => 'Bank Transfer',
                'transactionId'    => $validated['transaction_id'],
                'amount'           => $validated['amount'],
                'amountInWord'     => numberToWord($validated['amount']),
                'date'             => $date,
                'societyName'      => $society->users->where('type', 2)->first()->f_name,
                'societyLogo'      => $society->logo,
                'societyPhone'     => $society->phone,
                'societyEmail'     => $society->users->where('type', 2)->first()->email,
                'societyAddress'   => $society->address,
                'primaryColor'     => $conference->primary_color,
                'country'          => $authUser->userDetail->country_id,
                'signatureName'    => $conferenceSetting->name,
                'signature'        => $conferenceSetting->signature,
                'conferenceAmount' => null,
                'addons'           => [],
                'workshop'         => $workshopData,
                'accompany' => null
            ];
            Mail::to($authUser->email)->send(new UserRegistrationMail($mailData, $conference->conference_name));

            if (!empty($validated['payment_voucher'])) {
                $validated['payment_voucher'] = $this->file_service->fileUpload($validated['payment_voucher'], 'payment_voucher', 'workshop/payment-voucher');
            }

            $workshopRegistration = WorkshopRegistration::create($validated);
            return response()->json([
                'success' => true,
                'message' => 'Registration completed successfully!',
                'registration_id' => $workshopRegistration->id
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
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
