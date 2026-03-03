<?php

namespace App\Http\Controllers\Backend\Participant;

use App\Http\Controllers\Controller;
use App\Mail\Conference\RegisteredByUserMail;
use App\Models\Conference\AccompanyPerson;
use App\Models\Conference\ConferenceAddon;
use App\Models\Conference\ConferenceMemberTypePrice;
use App\Models\Conference\ConferenceRegistration;
use App\Models\Conference\ConferenceSetting;
use App\Models\Conference\Submission;
use App\Models\Payment\InternationalPayment;
use App\Models\Payment\NationalPayment;
use App\Models\Workshop\Workshop;
use App\Models\Workshop\WorkshopRegistration;
use App\Services\File\FileService;
use Exception;
use Illuminate\Http\Request;
use Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConferenceRegistrationController extends Controller
{
    public function __construct(protected FileService $file_service) {}

    public function index($society, $conference)
    {
        $registrations = ConferenceRegistration::where(['user_id' => current_user()->id, 'status' => 1, 'conference_id' => $conference->id])->get();
        $conference_registration = ConferenceRegistration::where(['user_id' => current_user()->id, 'conference_id' => $conference->id, 'status' => 1])->first();
        return view('backend.participant.conference-registration.index', compact('conference', 'society', 'registrations', 'conference_registration'));
    }

    public function create($society, $conference)
    {
        $checkPayment = null;
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
        $international_payemnt_setting = InternationalPayment::with('countries')->where('society_id', $conference->society_id)->first();
        $workshops = Workshop::with(['registrations' => function ($q) {
            $q->where('status', 1);
        }])
            ->where([
                'conference_id' => $conference->id,
                'status' => 1
            ])
            ->get()
            ->filter(function ($workshop) use ($membetType) {
                $currentUserId = current_user()->id;

                $checkRegistration = $workshop->registrations
                    ->where('user_id', $currentUserId)
                    ->first();

                if (!empty($checkRegistration)) {
                    return false;
                }

                $totalQuota = $workshop->no_of_participants;
                $appliedQuota = $workshop->registrations->where('verified_status', 1)->count();

                if ($appliedQuota >= $totalQuota) {
                    return false;
                }

                $price = DB::table('workshop_registration_prices')
                    ->where([
                        'workshop_id' => $workshop->id,
                        'member_type_id' => $membetType->id,
                    ])
                    ->first();

                if (empty($price) || empty($price->price)) {
                    return false;
                }

                return true;
            });

        // Get conference addons grouped by name with pricing for user's member type
        $conferenceAddons = ConferenceAddon::where([
            'conference_id' => $conference->id,
            'member_type_id' => $membetType->id,
            'status' => 1
        ])
            ->select('id', 'addon_name', 'early_bird_amount', 'regular_amount', 'on_site_amount', 'guest_amount', 'member_type_id')
            ->get()
            ->groupBy('addon_name')
            ->map(function ($group) {
                return $group->first(); // Take first item from each group (since all have same pricing for this member type)
            })
            ->values();
        // dd($conferenceAddons);

        // Get conference setting for addon availability
        $conferenceSetting = $conference->conferenceSetting;
        $addonAvailability = $conferenceSetting?->addon_availability ?? 'both';

        return view('backend.participant.conference-registration.create', compact('conference', 'amount', 'memberTypePrice', 'society', 'national_payemnt_setting', 'international_payemnt_setting', 'checkPayment', 'workshops', 'conferenceAddons', 'addonAvailability'));
    }

    public function getWorkshopPricing(Request $request)
    {
        // dd($request->all());
        try {
            $workshopId = $request->workshop_id;
            $memberTypeId = $request->member_type_id;

            if (!$workshopId || !$memberTypeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Workshop ID and Member Type ID are required'
                ]);
            }

            // Fetch workshop details
            $workshop = DB::table('workshops')->where('id', $workshopId)->first();

            if (!$workshop) {
                return response()->json([
                    'success' => false,
                    'message' => 'Workshop not found'
                ]);
            }

            // Fetch workshop pricing
            $workshopPricing = DB::table('workshop_registration_prices')
                ->where('workshop_id', $workshopId)
                ->where('member_type_id', $memberTypeId)
                ->first();

            if (!$workshopPricing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Workshop pricing not found for this member type'
                ]);
            }

            return response()->json([
                'success' => true,
                'workshop_name' => $workshop->workshop_title,
                'main_price' => $workshopPricing->price,
                'workshop_id' => $workshopId,
                'member_type_id' => $memberTypeId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching workshop pricing: ' . $e->getMessage()
            ]);
        }
    }

    public function store(Request $request, $society, $conference)
    {

        $rules = [
            'accompany_person' => 'nullable|numeric',
            'registrant_type' => 'required',
            'amount' => 'required',
            'payment_type' => 'required',
            'payment_voucher' => 'required|mimes:jpg,png,pdf',
            'transaction_id' => 'required|unique:conference_registrations,transaction_id'
        ];

        $message = [
            'transaction_id.unique' => 'Transaction/Reference Id already exist.',
        ];

        $validated = $request->validate($rules, $message);
        try {
            if (is_past($conference->regular_registration_deadline)) {
                return redirect()->back()->with('delete', 'Registration deadline has been ended.');
            } else {
                $checkDuplicateRegistration = ConferenceRegistration::where(['user_id' => current_user()->id, 'conference_id' => $conference->id, 'status' => 1])->first();
                $conferenceSetting = ConferenceSetting::where('conference_id', $conference->id)->first();
                $membetType = current_user()->societies->where('id', $conference->society_id)->first()?->pivot?->memberType;
                $memberTypePrice = ConferenceMemberTypePrice::where(['conference_id' => $conference->id, 'member_type_id' => $membetType->id])->first();
                if (empty($checkDuplicateRegistration)) {
                    $authUser = current_user();
                    $validated['user_id'] = $authUser->id;
                    $validated['verified_status'] = $validated['payment_type'] == 6 ? 0 : 1;
                    $validated['conference_id'] = $conference->id;
                    $validated['total_attendee'] = empty($request->accompany_person) ? 1 : $request->accompany_person + 1;
                    $validated['token'] = random_word(60);

                    // Process payment voucher if exists
                    if (!empty($validated['payment_voucher'])) {
                        $validated['payment_voucher'] = $this->file_service->fileUpload($validated['payment_voucher'], 'payment_voucher', 'conference/payment-voucher');
                    }
                    
                    // Use the conference base amount from the request if provided, otherwise calculate it
                    $conferenceAmount = '';
                    if (!empty($request->conference_base_amount)) {
                        $conferenceAmount = $request->conference_base_amount;
                    } else {
                        // Fallback to calculation if not provided (for backward compatibility)
                        if (!empty($conference)) {
                            if ($conference->early_bird_registration_deadline >= date('Y-m-d')) {
                                $conferenceAmount = !empty($memberTypePrice->early_bird_amount) ? $memberTypePrice->early_bird_amount : '';
                            } elseif ($conference->regular_registration_deadline >= date('Y-m-d')) {
                                $conferenceAmount = !empty($memberTypePrice->regular_amount) ? $memberTypePrice->regular_amount : '';
                            }
                        }
                    }

                    // Get addon availability setting
                    $addonAvailability = $conferenceSetting?->addon_availability ?? 'both';
                    $accompanyPersonCount = $request->accompany_person ?? 0;

                    $addonsData = [];
                    if (!empty($request->selected_addons)) {
                        $addons = explode(',', $request->selected_addons);
                        foreach ($addons as $addon) {
                            $parts = explode(':', $addon);
                            $addonId = $parts[0];
                            $mainAmount = $parts[1];
                            $guestAmount = isset($parts[2]) ? $parts[2] : $parts[1];
                            $includeGuest = isset($parts[3]) ? $parts[3] : '1';

                            $addonDetail = ConferenceAddon::find($addonId);
                            $addonName = $addonDetail->addon_name ?? 'Addon ' . $addonId;
                            
                            // Prepare addon data based on availability setting
                            if ($addonAvailability === 'participant_only') {
                                // Only show participant pricing
                                $addonsData[] = [
                                    'name'   => $addonName . ' (Participant Only)',
                                    'amount' => $mainAmount,
                                    'guest_amount' => 0,
                                    'include_guest' => false,
                                    'availability_type' => 'participant_only'
                                ];
                            } elseif ($addonAvailability === 'accompany_only') {
                                // Only show guest pricing
                                if ($accompanyPersonCount > 0) {
                                    $addonsData[] = [
                                        'name'   => $addonName . ' (Guests Only)',
                                        'amount' => 0,
                                        'guest_amount' => $guestAmount,
                                        'guest_count' => $accompanyPersonCount,
                                        'include_guest' => true,
                                        'availability_type' => 'accompany_only'
                                    ];
                                }
                            } else {
                                // Both - show based on include_guest flag
                                $addonsData[] = [
                                    'name'   => $addonName,
                                    'amount' => $mainAmount,
                                    'guest_amount' => $guestAmount,
                                    'guest_count' => $accompanyPersonCount,
                                    'include_guest' => $includeGuest == '1',
                                    'availability_type' => 'both'
                                ];
                            }
                        }
                    }


                    $workshopData = [];
                    if (!empty($request->selected_workshops)) {
                        $workshops = explode(',', $request->selected_workshops);
                        foreach ($workshops as $workshopInfos) {
                            [$workshopId, $mainPrice, $guestPrice] = explode(':', $workshopInfos);
                            $workshop = Workshop::find($workshopId);
                            $workshopData[] = [
                                'name'   => $workshop->workshop_title ?? 'Workshop',
                                'amount' => $mainPrice
                            ];
                        }
                    }

                    // $workshopData = null;
                    // if (!empty($request->workshop_id)) {
                    //     $workshop = Workshop::find($request->workshop_id);
                    //     $workshopData = [
                    //         'name'   => $workshop->workshop_title ?? 'Workshop',
                    //         'amount' => $request->workshop_amount
                    //     ];
                    // }
                    $accompanyData = null;
                    if (!empty($request->accompany_person)) {
                        $accompanyData = [
                            'accompany_person' => $validated['accompany_person'],
                            'amount'           => $memberTypePrice->guest_amount,
                        ];
                    }


                    $mailData = [
                        'conference_theme' => $conference->conference_theme,
                        'conference_name'  => $conference->conference_name,
                        'name' => $authUser->fullName($authUser),
                        'namePrefix' => $authUser->userDetail->namePrefix->prefix,
                        'email' => $authUser->email,
                        'paymentType' => 'Bank Transfer',
                        'transactionId' => $validated['transaction_id'],
                        'amount' => $validated['amount'],
                        'amountInWord' => numberToWord($validated['amount']),
                        'societyName'      => $society->users->where('type', 2)->first()->f_name,
                        'societyLogo'      => $society->logo,
                        'societyPhone'     => $society->phone,
                        'societyEmail'     => $society->users->where('type', 2)->first()->email,
                        'societyAddress'   => $society->address,
                        'primaryColor'     => $conference->primary_color,
                        'country'          => $authUser->userDetail->country_id,
                        'signatureName'    => $conferenceSetting?->name,
                        'signature'        => $conferenceSetting?->signature,
                        'conferenceAmount' => $conferenceAmount,
                        'addons'           => $addonsData,
                        'workshop'         => $workshopData,
                        'accompany' => $accompanyData,
                        'serviceCharge' => $authUser->userDetail->country_id != 125 ? $validated['amount'] * 0.035 : null
                    ];

                    DB::beginTransaction();
                    
                    // Create the conference registration record first
                    $conferenceRegistration = ConferenceRegistration::create($validated);
                    
                    // Now use the actual created_at timestamp for the receipt
                    $date = $conferenceRegistration->created_at->format('F j, Y');
                    $mailData['date'] = $date;

                    // Send email with CC if configured
                    $mail = Mail::to($authUser->email);
                    
                    // Add CC emails if configured
                    $conferenceSetting = $conference->conferenceSetting;
                    if ($conferenceSetting && !empty($conferenceSetting->conference_registration_cc_emails)) {
                        $ccEmails = getCcEmails($conferenceSetting->conference_registration_cc_emails);
                        if (!empty($ccEmails)) {
                            $mail->cc($ccEmails);
                        }
                    }
                    
                    $mail->send(new RegisteredByUserMail($mailData, $conference->conference_name));
                    // Insert Addons
                    if (!empty($request->selected_addons)) {
                        $addons = explode(',', $request->selected_addons);
                        $insertData = [];
                        
                        // Get addon availability setting
                        $addonAvailability = $conference->conferenceSetting?->addon_availability ?? 'both';

                        foreach ($addons as $addon) {
                            $parts = explode(':', $addon);
                            $addonId = $parts[0];
                            $amount = $parts[1]; // Main attendee amount
                            $guestAmount = isset($parts[2]) ? $parts[2] : $parts[1]; // Guest amount
                            $includeGuest = isset($parts[3]) && $parts[3] == '1' ? 1 : 0;

                            // When addon_availability is 'both' and includeGuest is true,
                            // create TWO entries: one for participant and one for guest
                            if ($addonAvailability === 'both' && $includeGuest) {
                                // Entry for participant
                                $insertData[] = [
                                    'conference_registration_id' => $conferenceRegistration->id,
                                    'conference_addon_id'        => $addonId,
                                    'amount'                     => $amount,
                                    'include_for_guests'         => false,
                                    'created_at'                 => now(),
                                    'updated_at'                 => now(),
                                ];
                                
                                // Entry for guest
                                $insertData[] = [
                                    'conference_registration_id' => $conferenceRegistration->id,
                                    'conference_addon_id'        => $addonId,
                                    'amount'                     => $guestAmount,
                                    'include_for_guests'         => true,
                                    'created_at'                 => now(),
                                    'updated_at'                 => now(),
                                ];
                            } else {
                                // Single entry (for participant_only, accompany_only, or both with includeGuest=false)
                                $insertData[] = [
                                    'conference_registration_id' => $conferenceRegistration->id,
                                    'conference_addon_id'        => $addonId,
                                    'amount'                     => $includeGuest ? $guestAmount : $amount,
                                    'include_for_guests'         => $includeGuest,
                                    'created_at'                 => now(),
                                    'updated_at'                 => now(),
                                ];
                            }
                        }

                        DB::table('conference_registration_addons')->insert($insertData);
                    }                    // Create Workshop Registration
                    if (!empty($request->selected_workshops)) {
                        $workshops = explode(',', $request->selected_workshops);
                        $insertWorkshopData = [];
                        foreach ($workshops as $workshopInfos) {
                            [$workshopId, $mainPrice, $guestPrice] = explode(':', $workshopInfos);
                            $insertWorkshopData[] = [
                                'user_id' => $authUser->id,
                                'workshop_id' => $workshopId,
                                'transaction_id' => $validated['transaction_id'],
                                'payment_type'  => $validated['payment_type'],
                                'amount'        => $mainPrice,
                                'token'         => random_word(60),
                                'verified_status' => 0,
                                'created_at'                 => now(),
                                'updated_at'                 => now(),
                            ];
                        }
                        DB::table('workshop_registrations')->insert($insertWorkshopData);
                    }

                    // if (!empty($request->workshop_id)) {
                    //     WorkshopRegistration::create([
                    //         'user_id'       => $authUser->id,
                    //         'workshop_id'   => $request->workshop_id,
                    //         'transaction_id' => $validated['transaction_id'],
                    //         'payment_type'  => $validated['payment_type'],
                    //         'amount'        => $request->workshop_amount,
                    //         'payment_voucher' => $validated['payment_voucher'],
                    //         'token'         => random_word(60),
                    //         'verified_status' => 0,
                    //     ]);
                    // }

                    DB::commit();
                    request()->session()->forget('onlinePayment');
                    return response()->json([
                        'success' => true,
                        'message' => 'Registration completed successfully!',
                        'registration_id' => $conferenceRegistration->id
                    ]);
                    // return redirect()->route('my-society.conference.index', [$society, $conference])->with('status', 'Successfully registered to conference.');
                } else {
                    // return redirect()->back()->with('delete', 'Registration already done for current conference.');
                    return response()->json([
                        'success' => false,
                        'message' => 'Registration already done for current conference.'
                    ], 500);
                }
            }
        } catch (Exception $e) {
            // dd($e);
            DB::rollBack();
            Log::channel('sentry')->error('Conference Registration Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
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
            // dd($request->all());
            if (is_past($conference->regular_registration_deadline)) {
                return redirect()->back()->with('delete', 'Registration deadline has ended.');
            }

            $checkDuplicateRegistration = ConferenceRegistration::where([
                'user_id'      => current_user()->id,
                'conference_id' => $conference->id,
                'status'       => 1
            ])->first();

            $conferenceSetting = ConferenceSetting::where('conference_id', $conference->id)->first();
            $membetType = current_user()->societies->where('id', $conference->society_id)->first()?->pivot?->memberType;
            $memberTypePrice = ConferenceMemberTypePrice::where(['conference_id' => $conference->id, 'member_type_id' => $membetType->id])->first();
            if (!empty($checkDuplicateRegistration)) {
                return redirect()->back()->with('delete', 'Registration already done for current conference.');
            }

            // Validation rules
            $rules = [
                'accompany_person' => 'nullable|numeric',
                'registrant_type'  => 'required',
                'amount'           => 'required',
                'payment_type'     => 'required',
                'transaction_id'   => 'required|unique:conference_registrations,transaction_id'
            ];

            $message = [
                'transaction_id.unique' => 'Transaction/Reference Id already exists.',
                'person_name.*.required' => 'Each person name is required.',
            ];

            $validated = $request->validate($rules, $message);

            // Authenticated user
            $authUser = current_user();
            $validated['user_id']         = $authUser->id;
            $validated['verified_status'] = 1;
            $validated['conference_id']   = $conference->id;
            $validated['total_attendee']  = empty($request->accompany_person) ? 1 : $request->accompany_person + 1;
            $validated['token']           = random_word(60);

            $onlinePayment = session()->get('onlinePayment');
            // dd($onlinePayment);
            // --- Determine Payment Type ---
            $paymentTypes = [
                1 => 'FonePay',
                2 => 'Moco',
                3 => 'Esewa',
                4 => 'Khalti',
                5 => 'Card Payment',
                7 => 'ConnectIPS'
            ];
            $paymentType = $paymentTypes[$request->payment_type] ?? 'Unknown';

            // Use the conference base amount from the request if provided, otherwise calculate it
            $conferenceAmount = '';
            if (!empty($onlinePayment['conference_base_amount'])) {
                $conferenceAmount = $onlinePayment['conference_base_amount'];
            } else {
                // Fallback to calculation if not provided (for backward compatibility)
                if (!empty($conference)) {
                    if ($conference->early_bird_registration_deadline >= date('Y-m-d')) {
                        $conferenceAmount = !empty($memberTypePrice->early_bird_amount) ? $memberTypePrice->early_bird_amount : '';
                    } elseif ($conference->regular_registration_deadline >= date('Y-m-d')) {
                        $conferenceAmount = !empty($memberTypePrice->regular_amount) ? $memberTypePrice->regular_amount : '';
                    }
                }
            }
            // --- Get addon availability setting ---
            $addonAvailability = $conferenceSetting?->addon_availability ?? 'both';
            $accompanyPersonCount = $onlinePayment['accompany_person'] ?? 0;

            // --- Collect Addons Info for Mail ---
            $addonsData = [];
            if (!empty($onlinePayment['selected_addons'])) {
                $addons = explode(',', $onlinePayment['selected_addons']);
                foreach ($addons as $addon) {
                    $parts = explode(':', $addon);
                    $addonId = $parts[0];
                    $mainAmount = $parts[1];
                    $guestAmount = isset($parts[2]) ? $parts[2] : $parts[1];
                    $includeGuest = isset($parts[3]) ? $parts[3] : '1';

                    $addonDetail = ConferenceAddon::find($addonId);
                    $addonName = $addonDetail->addon_name ?? 'Addon ' . $addonId;
                    
                    // Prepare addon data based on availability setting
                    if ($addonAvailability === 'participant_only') {
                        // Only show participant pricing
                        $addonsData[] = [
                            'name'   => $addonName . ' (Participant Only)',
                            'amount' => $mainAmount,
                            'guest_amount' => 0,
                            'include_guest' => false,
                            'availability_type' => 'participant_only'
                        ];
                    } elseif ($addonAvailability === 'accompany_only') {
                        // Only show guest pricing
                        if ($accompanyPersonCount > 0) {
                            $addonsData[] = [
                                'name'   => $addonName . ' (Guests Only)',
                                'amount' => 0,
                                'guest_amount' => $guestAmount,
                                'guest_count' => $accompanyPersonCount,
                                'include_guest' => true,
                                'availability_type' => 'accompany_only'
                            ];
                        }
                    } else {
                        // Both - show based on include_guest flag
                        $addonsData[] = [
                            'name'   => $addonName,
                            'amount' => $mainAmount,
                            'guest_amount' => $guestAmount,
                            'guest_count' => $accompanyPersonCount,
                            'include_guest' => $includeGuest == '1',
                            'availability_type' => 'both'
                        ];
                    }
                }
            }

            // --- Collect Workshop Info for Mail ---
            $workshopData = [];
            if (!empty($onlinePayment['selected_workshops'])) {
                $workshops = explode(',', $onlinePayment['selected_workshops']);
                foreach ($workshops as $workshopInfos) {
                    [$workshopId, $mainPrice, $guestPrice] = explode(':', $workshopInfos);
                    $workshop = Workshop::find($workshopId);
                    $workshopData[] = [
                        'name'   => $workshop->workshop_title ?? 'Workshop',
                        'amount' => $mainPrice
                    ];
                }
            }

            $accompanyData = null;
            if (!empty($request->accompany_person)) {
                $accompanyData = [
                    'accompany_person' => $validated['accompany_person'],
                    'amount'           => $memberTypePrice->guest_amount,
                ];
            }
            // --- Prepare Mail Data ---
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
                'societyName'      => $society->users->where('type', 2)->first()->f_name,
                'societyLogo'      => $society->logo,
                'societyPhone'     => $society->phone,
                'societyEmail'     => $society->users->where('type', 2)->first()->email,
                'societyAddress'   => $society->address,
                'primaryColor'     => $conference->primary_color,
                'country'          => $authUser->userDetail->country_id,
                'signatureName'    => $conferenceSetting?->name,
                'signature'        => $conferenceSetting?->signature,
                'conferenceAmount' => $conferenceAmount,
                'addons'           => $addonsData,
                'workshop'         => $workshopData,
                'accompany' => $accompanyData
            ];
            
            DB::beginTransaction();

            // Create Conference Registration record first
            $conference_registration = ConferenceRegistration::create($validated);
            
            // Now use the actual created_at timestamp for the receipt
            $date = $conference_registration->created_at->format('F j, Y');
            $mailData['date'] = $date;
            
            // Send Email
            $mail = Mail::to($authUser->email);
            
            // Add CC emails if configured
            $conferenceSetting = $conference->conferenceSetting;
            if ($conferenceSetting && !empty($conferenceSetting->conference_registration_cc_emails)) {
                $ccEmails = getCcEmails($conferenceSetting->conference_registration_cc_emails);
                if (!empty($ccEmails)) {
                    $mail->cc($ccEmails);
                }
            }
            
            $mail->send(new RegisteredByUserMail($mailData, $conference->conference_name));

            // Insert Addons
            if (!empty($onlinePayment['selected_addons'])) {
                $addons = explode(',', $onlinePayment['selected_addons']);
                $insertData = [];
                
                // Get addon availability setting
                $addonAvailability = $conference->conferenceSetting?->addon_availability ?? 'both';

                foreach ($addons as $addon) {
                    $parts = explode(':', $addon);
                    $addonId = $parts[0];
                    $amount = $parts[1]; // Main attendee amount
                    $guestAmount = isset($parts[2]) ? $parts[2] : $parts[1]; // Guest amount
                    $includeGuest = isset($parts[3]) && $onlinePayment['accompany_person'] > 0  && $parts[3] == '1' ? 1 : 0;

                    // When addon_availability is 'both' and includeGuest is true,
                    // create TWO entries: one for participant and one for guest
                    if ($addonAvailability === 'both' && $includeGuest) {
                        // Entry for participant
                        $insertData[] = [
                            'conference_registration_id' => $conference_registration->id,
                            'conference_addon_id'        => $addonId,
                            'amount'                     => $amount,
                            'include_for_guests'         => false,
                            'created_at'                 => now(),
                            'updated_at'                 => now(),
                        ];
                        
                        // Entry for guest
                        $insertData[] = [
                            'conference_registration_id' => $conference_registration->id,
                            'conference_addon_id'        => $addonId,
                            'amount'                     => $guestAmount,
                            'include_for_guests'         => true,
                            'created_at'                 => now(),
                            'updated_at'                 => now(),
                        ];
                    } else {
                        // Single entry (for participant_only, accompany_only, or both with includeGuest=false)
                        $insertData[] = [
                            'conference_registration_id' => $conference_registration->id,
                            'conference_addon_id'        => $addonId,
                            'amount'                     => $includeGuest ? $guestAmount : $amount,
                            'include_for_guests'         => $includeGuest,
                            'created_at'                 => now(),
                            'updated_at'                 => now(),
                        ];
                    }
                }

                DB::table('conference_registration_addons')->insert($insertData);
            }            // Create Workshop Registration
            if (!empty($onlinePayment['selected_workshops'])) {
                $workshops = explode(',', $onlinePayment['selected_workshops']);
                $insertWorkshopData = [];
                foreach ($workshops as $workshopInfos) {
                    [$workshopId, $mainPrice, $guestPrice] = explode(':', $workshopInfos);
                    $insertWorkshopData[] = [
                        'user_id' => $authUser->id,
                        'workshop_id' => $workshopId,
                        'transaction_id' => $validated['transaction_id'],
                        'payment_type'  => $validated['payment_type'],
                        'amount'        => $mainPrice,
                        'token'         => random_word(60),
                        'verified_status' => 1,
                        'created_at'                 => now(),
                        'updated_at'                 => now(),
                    ];
                }
                DB::table('workshop_registrations')->insert($insertWorkshopData);
                // WorkshopRegistration::create([
                //     'user_id'       => $authUser->id,
                //     'workshop_id'   => $onlinePayment['workshop_id'],
                //     'transaction_id' => $validated['transaction_id'],
                //     'payment_type'  => $validated['payment_type'],
                //     'amount'        => $onlinePayment['workshop_amount'],
                //     'token'         => random_word(60),
                //     'verified_status' => 1,
                // ]);
            }

            DB::commit();
            request()->session()->forget('onlinePayment');

            return redirect()
                ->route('my-society.conference.index', [$society, $conference])
                ->with('status', 'Successfully registered to conference.');
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
