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
use App\Models\WorkshopRating;
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
        // dd($request->all());
        try {
            $workshop = Workshop::whereId($request->workshop_id)->first();

            $rules = [
                'workshop_id' => 'required',
            ];
            if ($workshop->workshop_type == 1) {
                $rules['transaction_id'] = 'required|unique:workshop_registrations,transaction_id';
                $rules['amount'] = 'required';
                $rules['payment_type'] = 'required';
            } else {
                $rules['transaction_id'] = 'nullable|unique:workshop_registrations,transaction_id';
                $rules['amount'] = 'nullable';
                $rules['payment_type'] = 'nullable';
            }

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
            $conferenceSetting = ConferenceSetting::where('conference_id', $conference->id)->first();

            $workshopData = null;
            $workshopData[] = [
                'name'   => $workshop->workshop_title ?? 'Workshop',
                'amount' => $validated['amount'] ?? 0
            ];
            // dd($request->payment_type);
            $mailData = [
                'conference_theme' => $conference->conference_theme,
                'conference_name'  => $conference->conference_name,
                'name'             => $authUser->fullName($authUser),
                'namePrefix'       => $authUser->userDetail->namePrefix->prefix,
                'email'            => $authUser->email,
                'paymentType'      => $paymentType,
                'transactionId'    => $validated['transaction_id'] ?? 'N/A',
                'amount'           => $validated['amount'] ?? 0,
                'amountInWord'     => numberToWord($validated['amount'] ?? 0),
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
                'workshop_type'    => $workshop->workshop_type,
                'accompany' => null
            ];

            Mail::to($authUser->email)->send(new UserRegistrationMail($mailData, $conference->conference_name));

            WorkshopRegistration::create($validated);

            return redirect()->route('my-society.conference.workshop.index', [$society, $conference])->with('status', 'Successfully registered for workshop.');
        } catch (Exception $e) {
            // dd($e->getMessage());
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

    public function rating(Request $request, $society, $conference)
    {
        $registrant = WorkshopRegistration::with('workshop')->whereId($request->id)->first();
        // Check authorization
        if ($registrant->user_id !== auth()->id()) {
            abort(403);
        }

        $existingRating = WorkshopRating::where('user_id', auth()->id())
            ->where('workshop_id', $registrant->workshop_id)
            ->first();
        return view('backend.participant.workshop-registration.rating', compact('registrant', 'existingRating', 'society', 'conference'));
    }

    public function submitRating(Request $request, $society, $conference)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'workshop_registration_id' => 'required|exists:workshop_registrations,id'
        ]);

        $registrant = WorkshopRegistration::findOrFail($validated['workshop_registration_id']);

        // Check if user owns this registrant
        if ($registrant->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Create or update rating
        WorkshopRating::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'workshop_id' => $registrant->workshop_id
            ],
            [
                'workshop_registration_id' => $registrant->id,
                'rating' => $validated['rating'],
                'comment' => $validated['comment']
            ]
        );

        return response()->json([
            'message' => 'Thank you for rating this workshop!'
        ]);
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
