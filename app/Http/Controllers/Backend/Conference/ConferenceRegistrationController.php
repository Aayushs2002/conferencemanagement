<?php

namespace App\Http\Controllers\Backend\Conference;

use App\Exports\ConferenceRegistrationExport;
use App\Http\Controllers\Controller;
use App\Models\Conference\AccompanyPerson;
use App\Models\Conference\Attendance;
use App\Models\Conference\ConferenceRegistration;
use App\Models\Conference\ConferenceRegistrationKit;
use App\Models\Conference\Meal;
use App\Models\Conference\PassSetting;
use App\Models\ConferenceMemberTypeNameTag;
use App\Models\User;
use App\Models\User\ConferenceUserPassDesignation;
use App\Models\User\MemberType; 
use App\Models\User\NamePrefix;
use App\Models\User\Society;
use App\Models\User\UserDetail;
use App\Models\User\UserSociety;
use App\Services\File\FileService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class ConferenceRegistrationController extends Controller
{
    public function __construct(protected FileService $file_service) {}

    public function index(Request $request, $society, $conference)
    {
        // $conferenceDetail = conference_detail();
        // $societyDetail = society_detail();

        // if (is_super_admin() && empty($societyDetail)) {
        //     return redirect()->route('dashboard');
        // }

        // if (is_society_admin()) {
        //     $society_id = current_user()->societies->value('id');
        // } elseif (is_super_admin()) {
        //     $society_id = $societyDetail->id ?? null;
        // } else {
        //     return redirect()->route('dashboard');
        // }

        // if (empty($conferenceDetail)) {
        //     return redirect()->route('dashboard');
        // }
        $society_id = $society->id;
        $query = ConferenceRegistration::with([
            'user.societies' => function ($query) use ($society_id) {
                $query->where('society_id', $society_id);
            },
            'user.userDetail'
        ])
            ->where('conference_id', $conference->id)
            ->where('status', 1);

        if ($request->filled('registrant_type')) {
            $query->where('registrant_type', $request->registrant_type);
        }

        if ($request->filled('is_invited')) {
            $query->where('is_invited', $request->is_invited);
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('country_id')) {
            $query->whereHas('user.userDetail', function ($q) use ($request) {
                $q->where('country_id', $request->country_id);
            });
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $registrants = $query->latest()->get();

        return view('backend.conference.conference-registration.registrant', [
            'registrants' => $registrants,
            'conference' => $conference,
            'society' => $society,
            'filters' => $request->only(['registrant_type', 'is_invited', 'payment_type', 'from', 'to'])
        ]);
    }


    public function show(Request $request)
    {
        $registrant = ConferenceRegistration::whereId($request->id)->first();
        return view('backend.conference.conference-registration.view', compact('registrant'));
    }




    public function registerForExceptionalCase($society, $conference)
    {
        // $conferenceDetail = conference_detail();
        // if (empty($conferenceDetail)) {
        //     return redirect()->route('dashboard');
        // }
        $registeredUserIds = ConferenceRegistration::where('conference_id', $conference->id)->pluck('user_id');
        $society = Society::with(['users' => function ($query) use ($registeredUserIds) {
            $query->where('type', 3)
                ->whereNotIn('users.id', $registeredUserIds)
                ->orderByDesc('users.id');
        }])->where([
            'id' => $conference->society_id,
            'status' => 1
        ])->first();
        $users = $society ? $society->users : collect();


        return view('backend.conference.conference-registration.register-for-exceptional-case', compact('users', 'society', 'conference'));
    }

    public function registerForExceptionalCaseSubmit(Request $request, $society, $conference)
    {
        try {
            $rules = [
                'user_id' => 'required',
                'registrant_type' => 'required',
                'transaction_id' => 'required|unique:conference_registrations,transaction_id',
                'amount' => 'required|integer',
                'meal_type' => 'required',
                'additional_guests' => 'nullable|numeric',
                'payment_voucher' => 'nullable|mimes:jpg,png,pdf|max:250'
            ];

            if ($request->registrant_type == 2) {
                $rules['short_cv'] = 'required';
            }

            if ($request->additional_guests >= 1) {
                $rules['person_name.*'] = 'required';
            }

            $message = [
                'user_id.required' => 'The user field is required.',
                'transaction_id.unique' => 'Transaction/Reference Id already exist.',
                'person_name.*.required' => 'Each person name is required.',
            ];

            $validated = $request->validate($rules, $message);

            // for values start

            if (empty($validated['additional_guests'])) {
                $validated['total_attendee'] = 1;
            } else {
                $validated['total_attendee'] = $validated['additional_guests'] + 1;
            }
            $validated['conference_id'] = $conference->id;
            $validated['token'] = random_word(60);
            $validated['verified_status'] = 1;
            $validated['payment_type'] = 6;

            if (!empty($validated['payment_voucher'])) {

                $validated['payment_voucher'] = $this->file_service->fileUpload($validated['payment_voucher'], 'payment_voucher', 'conference/payment-voucher');
            }

            // for values end

            // $user = User::whereId($validated['user_id'])->first();
            // $mailData = [
            //     'namePrefix'  => $user->namePrefix->prefix ?? null,
            //     'conference_theme' => conference_detail()->conference_theme,
            //     'name' => $user->fullName($user),
            //     'email' => $user->email,
            // ];

            // Mail::to($user->email)->send(new RegistrationByUserMail($mailData));

            DB::beginTransaction();
            // insert table-1
            $registration = ConferenceRegistration::create($validated);

            // insert table-2
            if ($request->additional_guests >= 1) {
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

            return redirect()->back()->with('status', 'Successfully registered.');
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function addPerson(Request $request, $society, $conference)
    {
        $registration = ConferenceRegistration::whereId($request->id)->first();
        return view('backend.conference.conference-registration.add-person', compact('registration', 'society', 'conference'));
    }

    public function addPersonSubmit(Request $request)
    {
        try {
            $rules = [
                'additional_guests' => 'required|numeric',
            ];
            if ($request->additional_guests >= 1) {
                $rules['person_name.*'] = 'required';
            }

            $message = [
                'person_name.*.required' => 'Each person name is required.',
            ];

            $validated = $request->validate($rules, $message);
            $registration = ConferenceRegistration::where('id', $request->id)->first();

            $validated['total_attendee'] = $validated['additional_guests'] + $registration->total_attendee;

            $registration->update($validated);
            DB::beginTransaction();
            // insert table-1


            // insert table-2
            if ($request->additional_guests >= 1) {
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
            $type = 'success';
            $message = "Successfully Added";

            DB::commit();

            // return redirect()->back()->with('status', 'Successfully registered.');
        } catch (Exception $e) {
            $type = 'error';
            $message = $e->getMessage();
        }

        return response()->json(['type' => $type, 'message' => $message]);
    }

    public function convertRegistrantType(Request $request, $society, $conference)
    {
        $registration = ConferenceRegistration::whereId($request->id)->first();
        return view('backend.conference.conference-registration.convert-registrant-type', compact('registration', 'society', 'conference'));
    }

    public function convertRegistrantTypeSubmit(Request $request)
    {
        try {
            $rules = [
                'registrant_type' => 'required|numeric',
            ];

            $validated = $request->validate($rules);
            $registration = ConferenceRegistration::where('id', $request->id)->first();

            $registration->update($validated);

            $type = 'success';
            $message = "Registrant type Converted Successfully Added";

            DB::commit();

            // return redirect()->back()->with('status', 'Successfully registered.');
        } catch (Exception $e) {
            $type = 'error';
            $message = $e->getMessage();
        }

        return response()->json(['type' => $type, 'message' => $message]);
    }

    public function registrationOrInvitation($society, $conference)
    {
        // $conferenceDetail = conference_detail();
        // if (empty($conferenceDetail)) {
        //     return redirect()->route('dashboard');
        // }
        $prefixesAll = NamePrefix::whereStatus(1)->get();
        return view('backend.conference.conference-registration.registration-or-invitation', compact('prefixesAll', 'society', 'conference'));
    }

    public function registrationOrInvitationSubmit(Request $request)
    {
        try {
            // dd($request->all());
            $checkUser = User::whereEmail($request->email)->first();
            $conferenceRegistration = ConferenceRegistration::where(['conference_id' => conference_detail()->id, 'user_id' => $checkUser?->id, 'status' => 1])->first();
            if ($conferenceRegistration) {
                return redirect()->back()->withInput()->with('delete', 'User already registered for conference.');
            }
            $rules = [
                'name_prefix_id' => 'required',
                'gender' => 'required',
                'f_name' => 'required',
                'm_name' => 'nullable',
                'l_name' => 'required',
                'phone' => 'required',
                'designation_id' => 'nullable',
                'department_id' => 'nullable',
                'institution_id' => 'nullable',
                'address' => 'nullable',
                'member_type_id' => 'required',
                'registrant_type' => 'required',
                'additional_guests' => 'nullable|numeric',
                'country_id' => 'required',
                'meal_type' => 'required',
                'payment_voucher' => 'nullable|mimes:jpg,png,pdf|max:250',
                'email' => 'required|email|unique:users,email'
            ];

            if ($request->has('invited_guest')) {
                $rules['council_number'] = 'nullable';
                $rules['transaction_id'] = 'nullable|unique:conference_registrations,transaction_id';
                $rules['amount'] = 'nullable';
            } else {
                $rules['council_number'] = 'nullable';
                $rules['transaction_id'] = 'required|unique:conference_registrations,transaction_id';
                $rules['amount'] = 'required|numeric';
            }

            if ($request->registrant_type == 2) {
                $rules['description'] = 'required';
            }

            if ($request->additional_guests >= 1) {
                $rules['person_name.*'] = 'required';
            }

            $message = [
                'transaction_id.unique' => 'Transaction/Reference Id already exist.',
                'person_name.*.required' => 'Each person name is required.',
            ];

            $validated = $request->validate($rules, $message);

            // for values start

            $password = random_word(8);
            $validated['password'] = Hash::make($password);

            if (empty($validated['additional_guests'])) {
                $validated['total_attendee'] = 1;
            } else {
                $validated['total_attendee'] = $validated['additional_guests'] + 1;
            }
            $validated['conference_id'] = conference_detail()->id;
            $validated['token'] = random_word(60);
            $validated['verified_status'] = 1;
            $validated['payment_type'] = 6;

            if (!empty($validated['payment_voucher'])) {
                $validated['payment_voucher'] = $this->file_service->fileUpload($validated['payment_voucher'], 'payment_voucher', 'conference/payment-voucher');
            }
            // for values end

            // $middleName = !empty($validated['m_name']) ? $validated['m_name'] . ' ' : '';
            // $namePrefix = DB::table('name_prefixes')->whereId($validated['name_prefix_id'])->first()->prefix;
            // $data = [
            //     'namePrefix' => $namePrefix,
            //     'name' => $validated['f_name'] . ' ' . $middleName . $validated['l_name'],
            //     'email' => $validated['email'],
            //     'conference_theme' => conference_detail()->conference_theme,
            //     'password' => $password,
            //     'invitationType' => 1
            // ];
            // Mail::to($validated['email'])->send(new RegistrationMail($data));

            if ($request->has('invited_guest')) {
                $validated['is_invited'] = 1;
            }

            unset($validated['delegate']);
            DB::beginTransaction();
            // insert table-1
            $storeUser = User::create($validated);

            $validated['user_id'] = $storeUser->id;

            // insert table-2
            UserDetail::create($validated);
            $societyId = current_user()->societies->value('id');
            //insert table-3
            $storeUser->societies()->attach($societyId, [
                'member_type_id' => $validated['member_type_id'],
            ]);

            // insert table-4
            $registration = ConferenceRegistration::create($validated);

            // insert table-5
            if ($request->additional_guests >= 1) {
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

            return redirect()->back()->with('status', 'Successfully registered.');
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function excelExport(Request $request, $society, $conference)
    {

        $society_id = $society->id ?? null;

        $query = ConferenceRegistration::with([
            'user.societies' => function ($query) use ($society_id) {
                $query->where('society_id', $society_id);
            },
            'user.userDetail'
        ])
            ->where('conference_id', $conference->id)
            ->where('status', 1);

        if ($request->filled('registrant_type')) {
            $query->where('registrant_type', $request->registrant_type);
        }

        if ($request->filled('is_invited')) {
            $query->where('is_invited', $request->is_invited);
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('country_id')) {
            $query->whereHas('user.userDetail', function ($q) use ($request) {
                $q->where('country_id', $request->country_id);
            });
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $registrants = $query->latest()->get();
        return Excel::download(new ConferenceRegistrationExport($registrants),  'conferenceRegistration.xlsx');
    }

    public function generateIndividualPass($society, $conference, ConferenceRegistration $conferenceRegistration)
    {
        // dd($conference);
        $participant = $conferenceRegistration;
        $passSetting = PassSetting::where(['conference_id' => $conference->id, 'status' => 1])->first();
        $userSociety = $participant->user->societies->first();
        $memberType = $userSociety?->pivot?->memberType;
        $conferenceUserPassDesignation = ConferenceUserPassDesignation::where(['conference_id' => $conference->id, 'user_id' => $participant->user_id])->first();
        $conferenceMemberTypeNameTag = ConferenceMemberTypeNameTag::where(['conference_id' => $conference->id, 'member_type_id' => $memberType->id, 'registrant_type' => $participant->registrant_type])->first();
        if ($conferenceUserPassDesignation) {
            $designation = $conferenceUserPassDesignation->pass_designation;
        } else {
            $designation = $conferenceMemberTypeNameTag->name_tag;
        }
        if (!$passSetting) {
            return redirect()->back()->with('delete', 'Please Create Pass Setting');
        }
        return view('backend.conference.conference-registration.individual-pass', compact('participant', 'passSetting', 'designation'));
    }

    public function participantProfile($token)
    {
        $participant = ConferenceRegistration::where('token', $token)->first();
        $conferenceRegistrationKit = ConferenceRegistrationKit::where('conference_registration_id', $participant->id)->first();

        $checkAttendance = $participant
            ->attendances()
            ->where(['conference_registration_id' => $participant->id, 'status' => 1])
            ->whereDate('created_at', date('Y-m-d'))
            ->first();
        // dd($checkAttendance);
        $totalLunchRemaining = $participant->total_attendee;
        $totalDinnerRemaining = $participant->total_attendee;
        $checkMeal = $participant
            ->meals()
            ->where(['conference_registration_id' => $participant->id])
            ->whereDate('created_at', date('Y-m-d'))
            ->first();
        if (!empty($checkMeal)) {
            $totalLunchRemaining =
                $participant->total_attendee - $checkMeal->lunch_taken;
            $totalDinnerRemaining =
                $participant->total_attendee - $checkMeal->dinner_taken;
        }
        $passSetting = PassSetting::where('conference_id', $participant->conference_id)->first();
        return view('backend.conference.conference-registration.attendance-profile', compact('participant', 'checkAttendance', 'totalLunchRemaining', 'totalDinnerRemaining', 'conferenceRegistrationKit', 'passSetting'));
    }


    public function takeAttendance(Request $request)
    {
        try {
            $data['conference_registration_id'] = $request->participant_id;
            Attendance::create($data);
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            throw $e;
        }
    }


    public function takeConferenceKit(Request $request)
    {
        try {
            $data['conference_registration_id'] = $request->participant_id;
            ConferenceRegistrationKit::create($data);
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function takeMeal(Request $request)
    {
        try {
            $currentTime = date('H:i:s');
            $today = date('Y-m-d');

            $participant = ConferenceRegistration::find($request->participant_id);

            if (!$participant) {
                return response()->json(['success' => false, 'message' => 'Participant not found.'], 404);
            }

            $passSetting = PassSetting::where('conference_id', $participant->conference_id)->first();

            if (!$passSetting) {
                return response()->json(['success' => false, 'message' => 'Meal settings not found.'], 404);
            }

            $isLunch = ($currentTime >= $passSetting->lunch_start_time && $currentTime <= $passSetting->lunch_end_time);
            $isDinner = ($currentTime >= $passSetting->dinner_start_time && $currentTime <= $passSetting->dinner_end_time);

            if (!$isLunch && !$isDinner) {
                return response()->json(['success' => false, 'message' => 'Meal is not available at this time.'], 403);
            }

            $mealRecord = Meal::where('conference_registration_id', $request->participant_id)
                ->whereDate('created_at', $today)
                ->first();

            if (!$mealRecord) {
                // First meal record of the day
                $mealData = [
                    'conference_registration_id' => $request->participant_id,
                    'lunch_taken' => $isLunch ? 1 : 0,
                    'dinner_taken' => $isDinner ? 1 : 0,
                ];

                Meal::create($mealData);
                $remaining = $participant->total_attendee - 1;
            } else {
                // Update existing record
                if ($isLunch) {
                    $mealRecord->lunch_taken += 1;
                    $remaining = $participant->total_attendee - $mealRecord->lunch_taken;
                } else {
                    $mealRecord->dinner_taken += 1;
                    $remaining = $participant->total_attendee - $mealRecord->dinner_taken;
                }

                $mealRecord->save();
            }

            return response()->json([
                'success' => true,
                'remaining' => $remaining
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
