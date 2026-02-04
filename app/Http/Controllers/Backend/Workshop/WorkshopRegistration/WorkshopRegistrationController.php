<?php

namespace App\Http\Controllers\Backend\Workshop\WorkshopRegistration;

use App\Http\Controllers\Controller;
use App\Mail\Workshop\Registration\AcceptMail;
use App\Mail\Workshop\Registration\RegistrationByAdminMail;
use App\Mail\Workshop\Registration\RejectMail;
use App\Models\Conference\ConferenceSetting;
use App\Models\User;
use App\Models\User\Institution;
use App\Models\User\NamePrefix;
use App\Models\User\Society;
use App\Models\User\UserDetail;
use App\Models\Workshop\Workshop;
use App\Models\Workshop\WorkshopAttendance;
use App\Models\Workshop\WorkshopPassSetting;
use App\Models\Workshop\WorkshopRegistration;
use App\Services\File\FileService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class WorkshopRegistrationController extends Controller
{
    public function __construct(protected FileService $file_service) {}

    public function index(Request $request, $society, $conference, $workshop)
    {
        $query = WorkshopRegistration::with('user')->where(['workshop_registrations.workshop_id' => $workshop->id, 'workshop_registrations.registrant_type' => 1, 'workshop_registrations.status' => 1]);
        
        if ($request->filled('meal_type')) {
            $query->where('workshop_registrations.meal_type', $request->meal_type);
        }

        // Apply sorting
        if ($request->filled('sort')) {
            if ($request->sort == 'name_asc') {
                $query->leftJoin('users', 'workshop_registrations.user_id', '=', 'users.id')
                    ->orderByRaw('CASE WHEN workshop_registrations.user_id IS NULL THEN 1 ELSE 0 END')
                    ->orderBy('users.f_name', 'asc')
                    ->orderBy('users.l_name', 'asc')
                    ->select('workshop_registrations.*');
            } elseif ($request->sort == 'name_desc') {
                $query->leftJoin('users', 'workshop_registrations.user_id', '=', 'users.id')
                    ->orderByRaw('CASE WHEN workshop_registrations.user_id IS NULL THEN 1 ELSE 0 END')
                    ->orderBy('users.f_name', 'desc')
                    ->orderBy('users.l_name', 'desc')
                    ->select('workshop_registrations.*');
            } else {
                $query->orderByRaw('CASE WHEN workshop_registrations.user_id IS NULL THEN 1 ELSE 0 END')
                    ->latest('workshop_registrations.created_at');
            }
        } else {
            $query->orderByRaw('CASE WHEN workshop_registrations.user_id IS NULL THEN 1 ELSE 0 END')
                ->latest('workshop_registrations.created_at');
        }
        
        $registrations = $query->get();

        return view('backend.workshop.workshop-registration.index', compact('registrations', 'workshop', 'society', 'conference'));
    }

    public function view($society, $conference, Request $request)
    {
        $registrant = WorkshopRegistration::where('id', $request->id)->first();

        return view('backend.workshop.workshop-registration.view', compact('registrant'));
    }

    public function verifyForm($society, $conference, Request $request)
    {
        $registration = WorkshopRegistration::where('id', $request->id)->first();

        return view('backend.workshop.workshop-registration.verify-registrant', compact('registration', 'society', 'conference'));
    }

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

            $type = 'success';
            $workshopRegistration = WorkshopRegistration::whereId($request->id)->first();

            $mailData = [
                'name' => $workshopRegistration->user->userDetail->namePrefix->prefix.' '.$workshopRegistration->user->fullName($workshopRegistration->user),
                'workshopTitle' => $workshopRegistration->workshop->workshop_title,
                'conference_name' => $conference->conference_name,
            ];

            if ($request->verified_status == 1) {
                Mail::to($workshopRegistration->user->email)->send(new AcceptMail($mailData, $conference->conference_name));
                $message = 'Registrant Accepted Successfully.';
            } else {
                $mailData['remarks'] = $validated['remarks'];
                Mail::to($workshopRegistration->user->email)->send(new RejectMail($mailData, $conference->conference_name));
                $message = 'Registrant Rejected Successfully.';
            }
            $workshopRegistration->update($validated);
        } catch (Exception $e) {
            $type = 'error';
            $message = $e->getMessage();
        }

        return response()->json(['type' => $type, 'message' => $message]);
    }

    public function registerForExceptionalCase($society, $conference)
    {

        $workshops = Workshop::where(['conference_id' => $conference->id, 'approval_status' => 'approved', 'status' => 1])->get();
        $society = Society::with(['users' => function ($query) {
            $query->where('type', 3)->orderByDesc('id');
        }])->where([
            'id' => $conference->society_id,
            'status' => 1,
        ])->first();

        $users = $society ? $society->users : collect();

        return view('backend.workshop.workshop-registration.register-for-exceptional-case', compact('workshops', 'users', 'society', 'conference'));
    }

    public function registerForExceptionalCaseSubmit($society, $conference, Request $request)
    {
        try {
            $rules = [
                'workshop_id' => 'required',
                'user_id' => 'required',
                'transaction_id' => 'required|unique:workshop_registrations,transaction_id',
                'amount' => 'required|integer',
                'meal_type' => 'required',
                'payment_voucher' => 'nullable|mimes:jpg,png,pdf|max:250',
            ];

            $validated = $request->validate($rules);

            $checkUserRegistrationInWorkshop = WorkshopRegistration::where(['workshop_id' => $request->workshop_id, 'user_id' => $request->user_id, 'status' => 1])->first();

            if (empty($checkUserRegistrationInWorkshop)) {
                $validated['token'] = random_word(60);
                $validated['verified_status'] = 1;
                $validated['payment_type'] = 6;
                $date = \Carbon\Carbon::now()->format('F j, Y');

                if (! empty($validated['payment_voucher'])) {

                    $validated['payment_voucher'] = $this->file_service->fileUpload($validated['payment_voucher'], 'payment_voucher', 'workshop/payment-voucher');
                }
                $user = User::whereId($request->user_id)->first();
                $workshop = Workshop::whereId($validated['workshop_id'])->first();
                $conferenceSetting = ConferenceSetting::where('conference_id', $conference->id)->first();

                $workshopData = [];
                $workshopData[] = [
                    'name' => $workshop->workshop_title ?? 'Workshop',
                    'amount' => $validated['amount'],
                ];
                $mailData = [
                    'conference_theme' => $conference->conference_theme,
                    'conference_name' => $conference->conference_name,
                    'name' => $user->fullName($user),
                    'namePrefix' => $user->userDetail->namePrefix->prefix,
                    'email' => $user->email,
                    'paymentType' => 'Online Payment',
                    'transactionId' => $validated['transaction_id'],
                    'amount' => $validated['amount'],
                    'amountInWord' => numberToWord($validated['amount']),
                    'date' => $date,
                    'societyName' => $society->users->where('type', 2)->first()->f_name,
                    'societyLogo' => $society->logo,
                    'societyPhone' => $society->phone,
                    'societyEmail' => $society->users->where('type', 2)->first()->email,
                    'societyAddress' => $society->address,
                    'primaryColor' => $conference->primary_color,
                    'country' => $user->userDetail->country_id,
                    'signatureName' => $conferenceSetting->name,
                    'signature' => $conferenceSetting->signature,
                    'conferenceAmount' => null,
                    'addons' => [],
                    'workshop' => $workshopData,
                    'accompany' => null,
                    'type' => 2,
                ];

                DB::beginTransaction();

                Mail::to($user->email)->send(new RegistrationByAdminMail($mailData, $conference->conference_name));

                WorkshopRegistration::create($validated);
                logActivity($conference->id, 'Registered Workshop', $user->fullName($user).' is registered to workshop');

                DB::commit();

                return redirect()->back()->with('status', 'Successfully registered.');
            } else {
                return redirect()->back()->withInput()->with('delete', 'User already registered for this workshop.');
            }
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function registerForNewUser($society, $conference)
    {
        $workshops = Workshop::where(['conference_id' => $conference->id, 'approval_status' => 'approved', 'status' => 1])->get();
        $prefixesAll = NamePrefix::whereStatus(1)->get();

        return view('backend.workshop.workshop-registration.register-for-new-user', compact('workshops', 'prefixesAll', 'conference', 'society'));
    }

    public function registerForNewUserSubmit($society, $conference, Request $request)
    {
        try {
            $checkUser = User::whereEmail($request->email)->first();

            $workshop = WorkshopRegistration::where(['workshop_id' => $request->workshop_id, 'user_id' => $checkUser?->id])->first();
            if ($workshop) {
                return redirect()->back()->withInput()->with('delete', 'User already registered for this workshop.');
            }
            $rules = [
                'workshop_id' => 'required',
                'name_prefix_id' => 'required',
                'f_name' => 'required',
                'm_name' => 'nullable',
                'l_name' => 'required',
                'phone' => 'required',
                'amount' => 'required',
                'institution_id' => 'required',
                'address' => 'required',
                'designation_id' => 'required',
                'department_id' => 'required',
                'member_type_id' => 'required',
                'payment_voucher' => 'nullable|mimes:jpg,png,pdf|max:250',
                'council_number' => 'nullable',
                'transaction_id' => 'required|unique:workshop_registrations,transaction_id',
                'email' => 'required|email|unique:users,email',
                'country_id' => 'required',
                'certificate_required' => 'nullable',
            ];

            $validated = $request->validate($rules);

            // for values start
            $password = random_word(8);
            $validated['password'] = Hash::make($password);

            $validated['token'] = random_word(60);
            $validated['verified_status'] = 1;
            $validated['payment_type'] = 6;
            $validated['type'] = 3;
            $date = \Carbon\Carbon::now()->format('F j, Y');

            if (! empty($validated['payment_voucher'])) {
                $validated['payment_voucher'] = $this->file_service->fileUpload($validated['payment_voucher'], 'payment_voucher', 'workshop/payment-voucher');
            }
            // for values end

            DB::beginTransaction();
            // insert table-1
            $storeUser = User::create($validated);

            $validated['user_id'] = $storeUser->id;

            UserDetail::create($validated);

            // insert table-4
            $societyId = current_user()->societies->value('id');
            // insert table-3
            $storeUser->societies()->attach($societyId, [
                'member_type_id' => $validated['member_type_id'],
            ]);

            $workshop = Workshop::whereId($validated['workshop_id'])->first();
            $conferenceSetting = ConferenceSetting::where('conference_id', $conference->id)->first();

            $workshopData = [];
            $workshopData[] = [
                'name' => $workshop->workshop_title ?? 'Workshop',
                'amount' => $validated['amount'],
            ];
            $namePrefix = DB::table('name_prefixes')->whereId($validated['name_prefix_id'])->first()->prefix;
            $mailData = [
                'conference_theme' => $conference->conference_theme,
                'conference_name' => $conference->conference_name,
                'name' => $request->f_name.' '.$request->m_name.' '.$request->l_name,
                'namePrefix' => $namePrefix,
                'email' => $request->email,
                'paymentType' => 'Online Payment',
                'transactionId' => $validated['transaction_id'],
                'amount' => $validated['amount'],
                'amountInWord' => numberToWord($validated['amount']),
                'date' => $date,
                'societyName' => $society->users->where('type', 2)->first()->f_name,
                'societyLogo' => $society->logo,
                'societyPhone' => $society->phone,
                'societyEmail' => $society->users->where('type', 2)->first()->email,
                'societyAddress' => $society->address,
                'primaryColor' => $conference->primary_color,
                'country' => $request->country_id,
                'signatureName' => $conferenceSetting->name,
                'signature' => $conferenceSetting->signature,
                'conferenceAmount' => null,
                'addons' => [],
                'workshop' => $workshopData,
                'accompany' => null,
                'type' => 1,
            ];
            Mail::to($request->email)->send(new RegistrationByAdminMail($mailData, $conference->conference_name));

            // insert table-3
            WorkshopRegistration::create($validated);
            logActivity($conference->id, 'Registered Workshop', $request->f_name.' '.$request->m_name.' '.$request->l_name.' is registered to workshop');

            DB::commit();

            return redirect()->back()->with('status', 'Successfully registered.');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function generatePass($workshop)
    {
        // Increase memory and execution time limits for large datasets
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', '300');
        set_time_limit(300);
        
        $registrant_type = request('registrant_type');
        
        // Count first to determine if we should redirect to batch mode
        // $count = WorkshopRegistration::where([
        //     'workshop_id' => $workshop->id, 
        //     'registrant_type' => $registrant_type, 
        //     'status' => 1
        // ])->count();
        
        // If more than 15 registrations, automatically use batch mode
        // if ($count > 15) {
        //     return redirect()->route('workshop.generatePassBatch', [
        //         'workshop' => $workshop,
        //         'registrant_type' => $registrant_type,
        //         'batch' => 1
        //     ]);
        // }
        
        // Eager load relationships to prevent N+1 queries
        $registrants = WorkshopRegistration::with([
            'workshop.WorkshopVenueDetail',
            'workshop.conference.society.users',
            'user.userDetail.namePrefix'
        ])
        ->where(['workshop_id' => $workshop->id, 'registrant_type' => $registrant_type, 'status' => 1])
        ->get();
        
        $passSetting = WorkshopPassSetting::where(['conference_id' => $workshop->conference_id, 'status' => 1])->first();

        return view('backend.workshop.pass.registrant-pass', compact('registrants', 'passSetting'));
    }

    public function generatePassBatch($workshop)
    {
        // For very large datasets, generate passes in batches
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', '300');
        set_time_limit(300);
        
        $registrant_type = request('registrant_type');
        $batch = request('batch', 1); // Current batch number
        $perPage = 12; // Process 12 registrations at a time (reduced for low-resource servers)
        
        // Get total count
        $total = WorkshopRegistration::where([
            'workshop_id' => $workshop->id, 
            'registrant_type' => $registrant_type, 
            'status' => 1
        ])->count();
        
        // Calculate total batches
        $totalBatches = ceil($total / $perPage);
        
        // Get registrations for current batch with eager loading
        $registrants = WorkshopRegistration::with([
            'workshop.WorkshopVenueDetail',
            'workshop.conference.society.users',
            'user.userDetail.namePrefix'
        ])
        ->where(['workshop_id' => $workshop->id, 'registrant_type' => $registrant_type, 'status' => 1])
        ->skip(($batch - 1) * $perPage)
        ->take($perPage)
        ->get();
        
        $passSetting = WorkshopPassSetting::where(['conference_id' => $workshop->conference_id, 'status' => 1])->first();

        return view('backend.workshop.pass.registrant-pass', compact('registrants', 'passSetting'))
            ->with('batch', $batch)
            ->with('totalBatches', $totalBatches);
    }

    public function generateDummyPass(Request $request, $workshop)
    {
        $request->validate([
            'dummy_count' => 'required|integer|min:1|max:100',
            'registrant_type' => 'required|integer|in:1,2',
        ]);

        $dummyCount = $request->dummy_count;
        $registrantType = $request->registrant_type;

        // Create and save dummy registrant objects to database
        $savedRegistrants = collect();

        for ($i = 1; $i <= $dummyCount; $i++) {
            // Create and save dummy registration to database
            $dummyRegistrant = WorkshopRegistration::create([
                'workshop_id' => $workshop->id,
                'registrant_type' => $registrantType,
                'user_id' => null, // No user linked
                'token' => \Str::random(32),
                'status' => 1,
                // 'is_dummy' => 1,
                'verified_status' => 1, // Auto-verify dummy passes
                'payment_type' => null,
                'transaction_id' => 'DUMMY-'.\Str::upper(\Str::random(10)),
                'amount' => 0,
            ]);

            // Reload with workshop relationship
            $dummyRegistrant->load('workshop');

            // Create a mock user object with userDetail
            $dummyUserDetail = new class
            {
                public $namePrefix;

                public function __construct()
                {
                    $this->namePrefix = new class
                    {
                        public $prefix = '';
                    };
                }
            };

            // Create a mock fullName method by setting it as a closure
            $dummyUserModel = new class
            {
                public $userDetail;

                public function __construct()
                {
                    $this->userDetail = new class
                    {
                        public $namePrefix;

                        public function __construct()
                        {
                            $this->namePrefix = new class
                            {
                                public $prefix = '';
                            };
                        }
                    };
                }

                public function fullName($user)
                {
                    return '';
                }
            };

            // Set the relationship manually
            $dummyRegistrant->setRelation('user', $dummyUserModel);

            $savedRegistrants->push($dummyRegistrant);
        }

        $passSetting = WorkshopPassSetting::where(['conference_id' => $workshop->conference_id, 'status' => 1])->first();

        return view('backend.workshop.pass.registrant-pass', compact('passSetting'))->with('registrants', $savedRegistrants);
    }

    public function participantProfile($token)
    {
        $participant = WorkshopRegistration::where('token', $token)->first();
        $checkAttendance = $participant
            ->attendances()
            ->where(['workshop_registration_id' => $participant->id, 'status' => 1])
            ->whereDate('created_at', date('Y-m-d'))
            ->first();

        return view('backend.workshop.workshop-registration.attendance-profile', compact('participant', 'checkAttendance'));
    }

    public function takeAttendance(Request $request)
    {
        try {
            $data['workshop_registration_id'] = $request->participant_id;
            WorkshopAttendance::create($data);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function downloadVoucher($society, $conference, WorkshopRegistration $workshopRegistration)
    {
        $user = User::where('id', $workshopRegistration->user_id)->first();
        $date = \Carbon\Carbon::now()->format('F j, Y');
        $conferenceSetting = ConferenceSetting::where('conference_id', $conference->id)->first();

        $workshopData = [];
        if ($workshopRegistration) {
            $workshopData[] = [
                'name' => $workshopRegistration->workshop->workshop_title,
                'amount' => $workshopRegistration->amount,
            ];
        }

        $data = [
            'namePrefix' => $user->userDetail->prefix ?? null,
            'conference_theme' => $conference->conference_theme,
            'conference_name' => $conference->conference_name,
            'name' => $user->fullName($user),
            'namePrefix' => $user->userDetail->namePrefix->prefix,
            'email' => $user->email,
            'paymentType' => 'Online Payment',
            'transactionId' => $workshopRegistration->transaction_id,
            'amount' => $workshopRegistration->amount,
            'amountInWord' => numberToWord($workshopRegistration->amount),
            'date' => $date,
            'societyName' => $society->users->where('type', 2)->first()->f_name,
            'societyLogo' => $society->logo,
            'societyPhone' => $society->phone,
            'societyEmail' => $society->users->where('type', 2)->first()->email,
            'societyAddress' => $society->address,
            'primaryColor' => $conference->primary_color,
            'country' => $user->userDetail->country_id,
            'signatureName' => $conferenceSetting->name,
            'signature' => $conferenceSetting->signature,
            'conferenceAmount' => null,
            'addons' => [],
            'workshop' => $workshopData,
            'accompany' => null,
            'serviceCharge' => null,
        ];

        $pdf = Pdf::loadView('emails.conference.payment-voucher', ['data' => $data])
            ->setPaper('legal', 'portrait');

        return $pdf->download('payment-voucher.pdf');
    }

    public function destroy($society, $conference, $workshop, WorkshopRegistration $registration)
    {
        try {
            $registration->delete();

            return redirect()->back()->with('delete', 'Registration Deleted Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('delete', 'Failed to delete registration');
        }
    }

    public function edit($society, $conference, $workshop, WorkshopRegistration $registration)
    {
        $loadData = function ($relation, $model) use ($society) {
            if ($society && $society->$relation()->exists()) {
                return $society->$relation()->where('status', 1)->get();
            }

            return $model::where('status', 1)->get();
        };

        $institutions = $loadData('institutions', Institution::class);
        $designations = $loadData('designations', \App\Models\User\Designation::class);
        $departments = $loadData('departments', \App\Models\User\Department::class);
        $prefixesAll = $loadData('namePrefixes', NamePrefix::class);
        $memberTypes = \App\Models\User\MemberType::where(['society_id' => $society->id, 'status' => 1])->get();
        $countries = \App\Models\User\Country::where('status', 1)->get();

        // Check for custom institution, designation, department
        $userInstitution = null;
        $userDesignation = null;
        $userDepartment = null;

        if ($registration->user_id) {
            $userInstitution = \App\Models\User\UserInstitution::where('user_id', $registration->user_id)->first();
            $userDesignation = \App\Models\User\UserDesignation::where('user_id', $registration->user_id)->first();
            $userDepartment = \App\Models\User\UserDepartment::where('user_id', $registration->user_id)->first();
        }

        // Get all users for linking option (when user_id is null)
        $users = $society ? $society->users()->where('type', 3)->orderByDesc('id')->get() : collect();

        return view('backend.workshop.workshop-registration.edit', compact(
            'registration',
            'society',
            'conference',
            'workshop',
            'prefixesAll',
            'memberTypes',
            'countries',
            'institutions',
            'designations',
            'departments',
            'userInstitution',
            'userDesignation',
            'userDepartment',
            'users'
        ));
    }

    public function update(Request $request, $society, $conference, $workshop, WorkshopRegistration $registration)
    {
        try {
            $rules = [];

            // Fields required only for registrants (registrant_type = 1)
            if ($registration->registrant_type == 1) {
                $rules['transaction_id'] = 'required|unique:workshop_registrations,transaction_id,'.$registration->id;
                $rules['amount'] = 'required|integer';
                $rules['meal_type'] = 'nullable';
                $rules['payment_type'] = 'required';
                $rules['payment_voucher'] = 'nullable|mimes:jpg,png,pdf|max:250';
                $rules['verified_status'] = 'required';
                $rules['remarks'] = 'nullable';
            }

            // If user_id is null, we can either link to existing user or create new user
            if (empty($registration->user_id)) {
                if ($request->filled('existing_user_id')) {
                    // Link to existing user
                    $rules['existing_user_id'] = 'required|exists:users,id';
                } else {
                    // Create new user - validate user fields
                    $rules['name_prefix_id'] = 'required';
                    $rules['gender'] = 'required';
                    $rules['f_name'] = 'required';
                    $rules['m_name'] = 'nullable';
                    $rules['l_name'] = 'required';
                    $rules['phone'] = 'required';
                    $rules['institution_id'] = 'required';
                    $rules['address'] = 'required';
                    $rules['designation_id'] = 'required';
                    $rules['department_id'] = 'required';
                    $rules['member_type_id'] = 'required';
                    $rules['council_number'] = 'nullable';
                    $rules['email'] = 'required|email|unique:users,email';
                    $rules['country_id'] = 'required';
                }
            } else {
                // Update existing user
                $rules['name_prefix_id'] = 'required';
                $rules['gender'] = 'required';
                $rules['f_name'] = 'required';
                $rules['m_name'] = 'nullable';
                $rules['l_name'] = 'required';
                $rules['phone'] = 'required';
                $rules['institution_id'] = 'required';
                $rules['address'] = 'required';
                $rules['designation_id'] = 'required';
                $rules['department_id'] = 'required';
                $rules['member_type_id'] = 'required';
                $rules['council_number'] = 'nullable';
                $rules['email'] = 'required|email|unique:users,email,'.$registration->user_id;
                $rules['country_id'] = 'required';
            }

            $validated = $request->validate($rules);

            DB::beginTransaction();

            // Handle payment voucher
            if (! empty($validated['payment_voucher'])) {
                if ($registration->payment_voucher) {
                    $this->file_service->deleteFile($registration->payment_voucher, 'workshop/payment-voucher');
                }
                $validated['payment_voucher'] = $this->file_service->fileUpload($validated['payment_voucher'], 'payment_voucher', 'workshop/payment-voucher');
            }

            // Handle user linking or creation
            if (empty($registration->user_id)) {
                if ($request->filled('existing_user_id')) {
                    // Check if user is already registered for this workshop
                    $existingRegistration = WorkshopRegistration::where('workshop_id', $workshop->id)
                        ->where('user_id', $validated['existing_user_id'])
                        ->where('status', 1)
                        ->where('id', '!=', $registration->id)
                        ->first();

                    if ($existingRegistration) {
                        DB::rollBack();

                        return redirect()->back()->withInput()->with('delete', 'This user is already registered for this workshop.');
                    }

                    // Link to existing user
                    $registration->user_id = $validated['existing_user_id'];
                } else {
                    // Create new user
                    $password = random_word(8);
                    $userData = [
                        'f_name' => $validated['f_name'],
                        'm_name' => $validated['m_name'],
                        'l_name' => $validated['l_name'],
                        'email' => $validated['email'],
                        'password' => Hash::make($password),
                        'type' => 3,
                    ];

                    $newUser = User::create($userData);

                    // Handle "other" options
                    $institution_id = $request->institution_id == 'other' ? null : $validated['institution_id'];
                    $designation_id = $request->designation_id == 'other' ? null : $validated['designation_id'];
                    $department_id = $request->department_id == 'other' ? null : $validated['department_id'];

                    \App\Models\User\UserDetail::create([
                        'user_id' => $newUser->id,
                        'phone' => $validated['phone'],
                        'gender' => $validated['gender'],
                        'name_prefix_id' => $validated['name_prefix_id'],
                        'designation_id' => $designation_id,
                        'department_id' => $department_id,
                        'institution_id' => $institution_id,
                        'institute_address' => $validated['address'],
                        'council_number' => $validated['council_number'],
                        'country_id' => $validated['country_id'],
                    ]);

                    // Create custom institution, designation, department if "other" was selected
                    if ($request->institution_id == 'other') {
                        \App\Models\User\UserInstitution::create([
                            'user_id' => $newUser->id,
                            'institution_name' => $request->other_institution_name,
                        ]);
                    }
                    if ($request->designation_id == 'other') {
                        \App\Models\User\UserDesignation::create([
                            'user_id' => $newUser->id,
                            'designation_name' => $request->other_designation,
                        ]);
                    }
                    if ($request->department_id == 'other') {
                        \App\Models\User\UserDepartment::create([
                            'user_id' => $newUser->id,
                            'department_name' => $request->other_department,
                        ]);
                    }

                    $newUser->societies()->attach($society->id, [
                        'member_type_id' => $validated['member_type_id'],
                    ]);

                    $registration->user_id = $newUser->id;
                }
            } else {
                // Update existing user
                $user = User::findOrFail($registration->user_id);
                $user->update([
                    'f_name' => $validated['f_name'],
                    'm_name' => $validated['m_name'],
                    'l_name' => $validated['l_name'],
                    'email' => $validated['email'],
                ]);

                // Handle "other" options
                $institution_id = $request->institution_id == 'other' ? null : $validated['institution_id'];
                $designation_id = $request->designation_id == 'other' ? null : $validated['designation_id'];
                $department_id = $request->department_id == 'other' ? null : $validated['department_id'];

                $user->userDetail->update([
                    'name_prefix_id' => $validated['name_prefix_id'],
                    'gender' => $validated['gender'],

                    'phone' => $validated['phone'],
                    'designation_id' => $designation_id,
                    'department_id' => $department_id,
                    'institution_id' => $institution_id,
                    'institute_address' => $validated['address'],
                    'council_number' => $validated['council_number'],
                    'country_id' => $validated['country_id'],
                ]);

                // Create/Update custom institution, designation, department if "other" was selected
                if ($request->institution_id == 'other') {
                    \App\Models\User\UserInstitution::updateOrCreate(
                        ['user_id' => $user->id],
                        ['institution_name' => $request->other_institution_name]
                    );
                } else {
                    \App\Models\User\UserInstitution::where('user_id', $user->id)->delete();
                }

                if ($request->designation_id == 'other') {
                    \App\Models\User\UserDesignation::updateOrCreate(
                        ['user_id' => $user->id],
                        ['designation_name' => $request->other_designation]
                    );
                } else {
                    \App\Models\User\UserDesignation::where('user_id', $user->id)->delete();
                }

                if ($request->department_id == 'other') {
                    \App\Models\User\UserDepartment::updateOrCreate(
                        ['user_id' => $user->id],
                        ['department_name' => $request->other_department]
                    );
                } else {
                    \App\Models\User\UserDepartment::where('user_id', $user->id)->delete();
                }

                $user->societies()->syncWithoutDetaching([
                    $society->id => ['member_type_id' => $validated['member_type_id']],
                ]);
            }

            // Update workshop registration
            $registrationData = [
                'user_id' => $registration->user_id, // Now set with either linked or created user
            ];

            // Add payment fields only for registrants (registrant_type = 1)
            if ($registration->registrant_type == 1) {
                $registrationData['transaction_id'] = $validated['transaction_id'];
                $registrationData['amount'] = $validated['amount'];
                $registrationData['meal_type'] = $validated['meal_type'];
                $registrationData['payment_type'] = $validated['payment_type'];
                $registrationData['verified_status'] = $validated['verified_status'];
                $registrationData['remarks'] = $validated['remarks'];
                $registrationData['payment_voucher'] = $validated['payment_voucher'] ?? $registration->payment_voucher;
            }

            $registration->update($registrationData);

            DB::commit();

            // Redirect based on registrant type
            if ($registration->registrant_type == 2) {
                // Trainer - redirect to trainer index
                return redirect()->route('workshop.workshop-trainer.index', [$society, $conference, $workshop])
                    ->with('status', 'Trainer Profile Updated Successfully');
            } else {
                // Registrant - redirect to registration index
                return redirect()->route('workshop.workshop-registration.index', [$society, $conference, $workshop])
                    ->with('status', 'Workshop Registration Updated Successfully');
            }
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('delete', 'Error: '.$e->getMessage());
        }
    }
}
