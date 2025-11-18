<?php

namespace App\Http\Controllers\Backend\Conference;

use App\Exports\ConferenceRegistrationExport;
use App\Exports\ImportLogExport;
use App\Http\Controllers\Controller;
use App\Imports\ConferenceRegistationImport;
use App\Mail\Conference\ExceptionalRegistrationMail;
use App\Mail\Conference\RegistrantAcceptMail;
use App\Mail\Conference\RegistrantRejectMail;
use App\Mail\Conference\RegistrationMail;
use App\Models\Conference\AccompanyPerson; 
use App\Models\Conference\Attendance;
use App\Models\Conference\ConferenceAddon;
use App\Models\Conference\ConferenceMemberTypePrice;
use App\Models\Conference\ConferenceRegistration;
use App\Models\Conference\ConferenceRegistration_addon;
use App\Models\Conference\ConferenceRegistrationKit;
use App\Models\Conference\ConferenceSetting;
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
use App\Models\Workshop\WorkshopRegistration;
use App\Services\File\FileService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ConferenceRegistrationController extends Controller
{
    public function __construct(protected FileService $file_service) {}

    public function index(Request $request, $society, $conference)
    {
        $society_id = $society->id;
        $query = ConferenceRegistration::with([
            'user' => function ($query) use ($society_id) {
                $query->with([
                    'userDetail.namePrefix',
                    'userDetail.country',
                    'userDetail.designation',
                    'userDetail.institution',
                    'societies' => function ($q) use ($society_id) {
                        $q->where('society_id', $society_id)
                          ->withPivot('member_type_id');
                    },
                    'societyUsers.memberType' => function ($q) use ($society_id) {
                        $q->where('society_id', $society_id);
                    }
                ]);
            },
            'conference',
            'accompanyPersons',
            'addons'
        ])
            ->where('conference_id', $conference->id)
            ->where('status', 1);

        if ($request->filled('registrant_type')) {
            $query->where('registrant_type', $request->registrant_type);
        }
        if ($request->filled('meal_type')) {
            $query->where('meal_type', $request->meal_type);
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
        if ($request->filled('prefix')) {
            $query->whereHas('user.userDetail', function ($q) use ($request) {
                $q->where('name_prefix_id', $request->prefix);
            });
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        if ($request->filled('member_type_id')) {
            $query->whereHas('user.societies', function ($q) use ($request, $society_id) {
                $q->where('society_id', $society_id)
                    ->where('member_type_id', $request->member_type_id);
            });
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

    public function edit($society, $conference, ConferenceRegistration $registrant)
    {
        $prefixesAll = NamePrefix::whereStatus(1)->get();
        $conferenceAddons = ConferenceAddon::where('conference_id', $conference->id)->get();
        $memberTypes = MemberType::where(['society_id' => $society->id, 'status' => 1])->get();
        $countries = \App\Models\User\Country::where('status', 1)->get();
        $institutions = \App\Models\User\Institution::where('status', 1)->get();
        $designations = \App\Models\User\Designation::where('status', 1)->get();
        $departments = \App\Models\User\Department::where('status', 1)->get();
        $name_prefiexs = NamePrefix::where('status', 1)->get();

        return view('backend.conference.conference-registration.edit', compact(
            'registrant',
            'society',
            'conference',
            'prefixesAll',
            'conferenceAddons',
            'memberTypes',
            'countries',
            'institutions',
            'designations',
            'departments',
            'name_prefiexs'
        ));
    }

    public function update(Request $request, $society, $conference, ConferenceRegistration $registrant)
    {
        try {
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
                'payment_type' => 'required|in:1,2,3,4,5,6',
                'payment_voucher' => 'nullable|mimes:jpg,png,pdf|max:250',
                'email' => 'required|email|unique:users,email,' . $registrant->user_id,
                'council_number' => 'nullable',
                'transaction_id' => 'required|unique:conference_registrations,transaction_id,' . $registrant->id,
                'amount' => 'required|numeric',
            ];

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

            if (empty($validated['additional_guests'])) {
                $validated['total_attendee'] = 1;
            } else {
                $validated['total_attendee'] = $validated['additional_guests'] + 1;
            }

            if (!empty($validated['payment_voucher'])) {
                // Delete old voucher if exists
                if ($registrant->payment_voucher) {
                    $this->file_service->deleteFile($registrant->payment_voucher, 'conference/payment-voucher');
                }
                $validated['payment_voucher'] = $this->file_service->fileUpload($validated['payment_voucher'], 'payment_voucher', 'conference/payment-voucher');
            }

            DB::beginTransaction();

            // Update user data
            $user = User::findOrFail($registrant->user_id);
            $user->update([
                'name_prefix_id' => $validated['name_prefix_id'],
                'f_name' => $validated['f_name'],
                'm_name' => $validated['m_name'],
                'l_name' => $validated['l_name'],
                'gender' => $validated['gender'],
                'email' => $validated['email'],
            ]);

            // Update user details
            $user->userDetail->update([
                'phone' => $validated['phone'],
                'designation_id' => $validated['designation_id'],
                'department_id' => $validated['department_id'],
                'institution_id' => $validated['institution_id'],
                'institute_address' => $validated['address'],
                'council_number' => $validated['council_number'],
                'country_id' => $validated['country_id'],
            ]);

            // Update user society membership
            $user->societies()->syncWithoutDetaching([
                $society->id => ['member_type_id' => $validated['member_type_id']]
            ]);

            // Update conference registration
            $registrant->update([
                'registrant_type' => $validated['registrant_type'],
                'total_attendee' => $validated['total_attendee'],
                'meal_type' => $validated['meal_type'],
                'payment_type' => $validated['payment_type'],
                'transaction_id' => $validated['transaction_id'],
                'amount' => $validated['amount'],
                'payment_voucher' => $validated['payment_voucher'] ?? $registrant->payment_voucher,
                'short_cv' => $validated['description'] ?? $registrant->short_cv,
            ]);

            // Update accompany persons
            if ($request->additional_guests >= 1) {
                // Delete existing accompany persons
                AccompanyPerson::where('conference_registration_id', $registrant->id)->delete();
                
                // Insert new ones
                $insertArray = [];
                foreach ($validated['person_name'] as $key => $value) {
                    $array['conference_registration_id'] = $registrant->id;
                    $array['person_name'] = $value;
                    $array['created_at'] = now();
                    $array['updated_at'] = now();
                    $insertArray[] = $array;
                }
                AccompanyPerson::insert($insertArray);
            } else {
                // Remove all accompany persons if additional_guests is 0
                AccompanyPerson::where('conference_registration_id', $registrant->id)->delete();
            }

            // Update addons
            if ($request->conference_addon_id) {
                // Delete existing addons
                ConferenceRegistration_addon::where('conference_registration_id', $registrant->id)->delete();
                
                // Insert new ones
                foreach ($request->conference_addon_id as $addon_id) {
                    $addon = ConferenceAddon::findOrFail($addon_id);
                    ConferenceRegistration_addon::create([
                        'conference_registration_id' => $registrant->id,
                        'conference_addon_id' => $addon_id,
                        'amount' => $user->userDetail->country_id == 125 ? $addon->addon_national_amount : $addon->addon_international_amount,
                    ]);
                }
            } else {
                // Remove all addons if none selected
                ConferenceRegistration_addon::where('conference_registration_id', $registrant->id)->delete();
            }

            $middleName = !empty($validated['m_name']) ? $validated['m_name'] . ' ' : '';
            logActivity($conference->id, 'Updated Conference Registration', $validated['f_name'] . ' ' . $middleName . $validated['l_name'] . ' registration updated');
            
            DB::commit();

            return redirect()->route('conference.conference-registration.index', [$society, $conference])
                ->with('status', 'Conference registration updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('delete', 'Failed to update registration: ' . $e->getMessage());
        }
    }

    public function deleteVoucher($society, $conference, ConferenceRegistration $registrant)
    {
        try {
            if ($registrant->payment_voucher) {
                $this->file_service->deleteFile($registrant->payment_voucher, 'conference/payment-voucher');
                $registrant->update(['payment_voucher' => null]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Payment voucher deleted successfully.'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'No voucher found to delete.'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete voucher: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteAccompanyPerson($society, $conference, AccompanyPerson $accompanyPerson)
    {
        try {
            $registration = ConferenceRegistration::findOrFail($accompanyPerson->conference_registration_id);
            $personName = $accompanyPerson->person_name;
            
            // Hard delete the accompany person
            $accompanyPerson->delete();
            
            // Update total attendee count
            $activeAccompanyCount = AccompanyPerson::where('conference_registration_id', $registration->id)->count();
            
            $registration->update([
                'total_attendee' => $activeAccompanyCount + 1 // +1 for the registrant
            ]);
            
            logActivity($conference->id, 'Deleted Accompany Person', 'Deleted accompany person: ' . $personName . ' from ' . $registration->user->fullName($registration->user) . ' registration');
            
            return response()->json([
                'success' => true,
                'message' => 'Accompany person deleted successfully.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete accompany person: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importExcel(Request $request, $society, $conference)
    {
        return view('backend.conference.conference-registration.import-registrant', compact('society', 'conference'));
    }

    public function importExcelSubmit(Request $request, $society, $conference)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'excel_file.required' => 'Please upload an Excel file.',
            'excel_file.mimes' => 'Only Excel files (xlsx, xls, csv) are allowed.',
            'excel_file.max' => 'The file size should not exceed 5MB.',
        ]);

        set_time_limit(600);
        ini_set('memory_limit', '1024M');

        $import = new ConferenceRegistationImport($society, $conference);

        try {
            Excel::import($import, $request->file('excel_file'));
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }

        if (!empty($import->log)) {
            $fileName = 'import_skipped_log_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

            Excel::store(new ImportLogExport($import->log), $fileName, 'public_uploads');

            return response()->json([
                'type' => 'log',
                'file' => url($fileName),
                'message' => 'Some rows were skipped, download the log file.'
            ]);
        }

        return response()->json([
            'type' => 'success',
            'message' => 'Excel file imported successfully.'
        ]);
    }

    public function registerForExceptionalCase($society, $conference)
    {
        $registeredUserIds = ConferenceRegistration::where('conference_id', $conference->id)->pluck('user_id');
        $society = Society::with(['users' => function ($query) use ($registeredUserIds) {
            $query->where('type', 3)
                ->whereNotIn('users.id', $registeredUserIds)
                ->orderByDesc('users.id');
        }])->where([
            'id' => $conference->society_id,
            'status' => 1
        ])->first();
        $conferenceAddons = ConferenceAddon::where('conference_id', $conference->id)->get();
        $users = $society ? $society->users : collect();


        return view('backend.conference.conference-registration.register-for-exceptional-case', compact('users', 'society', 'conference', 'conferenceAddons'));
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
            $date = \Carbon\Carbon::now()->format('F j, Y');

            if (!empty($validated['payment_voucher'])) {

                $validated['payment_voucher'] = $this->file_service->fileUpload($validated['payment_voucher'], 'payment_voucher', 'conference/payment-voucher');
            }

            // for values end 
            $conferenceSetting = ConferenceSetting::where('conference_id', $conference->id)->first();

            $user = User::whereId($validated['user_id'])->first();
            $mailData = [
                'namePrefix'  => $user->userDetail->prefix ?? null,
                'conference_theme' => $conference->conference_theme,
                'conference_name' => $conference->conference_name,
                'name' => $user->fullName($user),
                'namePrefix' => $user->userDetail->namePrefix?->prefix,
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
                'conferenceAmount' => $validated['amount'],
                'addons'           => [],
                'workshop'         => [],
                'accompany' => null,
                'serviceCharge' =>  null
            ];

            Mail::to($user->email)->send(new ExceptionalRegistrationMail($mailData, $conference->conference_name));

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
            if ($request->conference_addon_id) {
                foreach ($request->conference_addon_id as $addon_id) {
                    $addon = ConferenceAddon::where('id', $request->conference_addon_id)->first();
                    // dd($addon);
                    ConferenceRegistration_addon::create([
                        'conference_registration_id' => $registration->id,
                        'conference_addon_id' => $addon_id,
                        'amount' => $user->userDetail->country_id == 125 ? $addon->addon_national_amount : $addon->addon_international_amount,
                    ]);
                }
            }
            logActivity($conference->id, 'Registered Conference', $user->fullName($user) . ' is registered to conference');

            DB::commit();

            return redirect()->back()->with('status', 'Successfully registered.');
        } catch (Exception $e) {
            DB::rollBack();
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

            logActivity($registration->conference_id, 'Add Person','Added ' .$validated['additional_guests'] . ' Guests to ' . $registration->user->fullName($registration->user) . ' is registered to conference');

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

    public function verifyForm($society, $conference, Request $request)
    {
        $registration = ConferenceRegistration::whereId($request->id)->first();
        return view('backend.conference.conference-registration.verify', compact('registration', 'society', 'conference'));
    }

    public function verifyRegistrant(Request $request, $society, $conference, ConferenceRegistration $conference_registration)
    {
        try {
            $rules = [
                'verified_status' => 'required',
            ];

            if ($request->verified_status == 2) {
                $rules['remarks'] = 'required';
            }
            $validated = $request->validate($rules);

            $conference_registration = ConferenceRegistration::whereId($request->id)->first();
            $data = [
                'name' => $conference_registration->user->fullName($conference_registration->user),
                'namePrefix' => $conference_registration->user->userDetail->namePrefix->prefix,
                'conference_theme' => $conference_registration->conference->conference_theme,
                'registrant_type' => $conference_registration->registrant_type,
                'conference_name' => $conference->conference_name,
            ];

            if ($request->verified_status == 1) {
                Mail::to($conference_registration->user->email)->send(new RegistrantAcceptMail($data, $conference->conference_name));

                $conference_registration->update($validated);
            } else {
                $data['remarks'] = $validated['remarks'];
                Mail::to($conference_registration->user->email)->send(new RegistrantRejectMail($data, $conference->conference_name));

                $conference_registration->update($validated);
            }

            $type = 'success';
            if ($conference_registration->registrant_type == 1) {
                $message = "Attendee Updated Successfully";
            } else {
                $message = "Presenter Updated Successfully";
            }
        } catch (Exception $e) {
            $type = 'error';
            $message = $e->getMessage();
        }
        return response()->json(['type' => $type, 'message' => $message]);
    }

    public function registrationOrInvitation($society, $conference)
    {
        $prefixesAll = NamePrefix::whereStatus(1)->get();
        $conferenceAddons = ConferenceAddon::where('conference_id', $conference->id)->get();

        return view('backend.conference.conference-registration.registration-or-invitation', compact('prefixesAll', 'society', 'conference', 'conferenceAddons'));
    }

    public function registrationOrInvitationSubmit(Request $request, $society, $conference)
    {
        try {
            // dd($request->all());
            $checkUser = User::whereEmail($request->email)->first();
            $conferenceRegistration = ConferenceRegistration::where(['conference_id' => $conference->id, 'user_id' => $checkUser?->id, 'status' => 1])->first();
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
            $invitationToken = bin2hex(random_bytes(32));
            $validated['conference_id'] = $conference->id;
            $validated['token'] = random_word(60);
            $validated['verified_status'] = 1;
            $validated['payment_type'] = 6;
            $validated['invitation_response_token'] = $invitationToken;

            $date = \Carbon\Carbon::now()->format('F j, Y');

            if (!empty($validated['payment_voucher'])) {
                $validated['payment_voucher'] = $this->file_service->fileUpload($validated['payment_voucher'], 'payment_voucher', 'conference/payment-voucher');
            }
            $conferenceSetting = ConferenceSetting::where('conference_id', $conference->id)->first();

            // for values end

            $middleName = !empty($validated['m_name']) ? $validated['m_name'] . ' ' : '';
            $namePrefix = DB::table('name_prefixes')->whereId($validated['name_prefix_id'])->first()->prefix;
            $data = [
                'namePrefix' => $namePrefix,
                'name' => $validated['f_name'] . ' ' . $middleName . $validated['l_name'],
                'email' => $validated['email'],
                'password' => $password,
                'conference_theme' => $conference->conference_theme,
                'conference_name' => $conference->conference_name,
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
                'country' => $validated['country_id'],
                'signatureName' => $conferenceSetting->name,
                'signature' => $conferenceSetting->signature,
                'conferenceAmount' => $validated['amount'],
                'addons'           => [],
                'workshop'         => [],
                'accompany' => null,
                'serviceCharge' =>  null,
                'invitationType' => 1,
                'is_invited' => $request->has('invited_guest') ? 1 : 0,
                'invitation_token' => $invitationToken,
                'invitation_url' => route('invitation.show', $invitationToken)
            ];
            Mail::to($validated['email'])->send(new RegistrationMail($data, $conference->conference_name));

            if ($request->has('invited_guest')) {
                $validated['is_invited'] = 1;
            }

            unset($validated['delegate']);
            DB::beginTransaction();
            // insert table-1
            $validated['type'] = 3;
            $storeUser = User::create($validated);

            $validated['user_id'] = $storeUser->id;

            // insert table-2
            UserDetail::create($validated);
            // $societyId = current_user()->societies->value('id');
            //insert table-3
            $storeUser->societies()->attach($society->id, [
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
            if ($request->conference_addon_id) {
                foreach ($request->conference_addon_id as $addon_id) {
                    $addon = ConferenceAddon::where('id', $request->conference_addon_id)->first();
                    // dd($addon);
                    ConferenceRegistration_addon::create([
                        'conference_registration_id' => $registration->id,
                        'conference_addon_id' => $addon_id,
                        'amount' => $storeUser->userDetail->country_id == 125 ? $addon->addon_national_amount : $addon->addon_international_amount,
                    ]);
                }
            }
            logActivity($conference->id, $request->has('invited_guest') ? 'Invited Conference' : 'Registered Conference', $validated['f_name'] . ' ' . $middleName . $validated['l_name'] . ' is registered to conference');
            DB::commit();

            return redirect()->back()->with('status', 'Successfully registered.');
        } catch (Exception $e) {
            DB::rollBack();
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

    public function generatePass(Request $request, $society, $conference)
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

        $passSetting = PassSetting::where(['conference_id' => $conference->id, 'status' => 1])->first();

        $registrantsWithDesignation = $registrants->map(function ($participant) use ($conference) {
            $userSociety = $participant->user->societies->first();
            $memberType = $userSociety?->pivot?->memberType;

            $conferenceUserPassDesignation = ConferenceUserPassDesignation::where([
                'conference_id' => $conference->id,
                'user_id' => $participant->user_id
            ])->first();

            $conferenceMemberTypeNameTag = null;
            if ($memberType) {
                $conferenceMemberTypeNameTag = ConferenceMemberTypeNameTag::where([
                    'conference_id' => $conference->id,
                    'member_type_id' => $memberType->id,
                    'registrant_type' => $participant->registrant_type
                ])->first();
            }

            if ($conferenceUserPassDesignation) {
                $designation = $conferenceUserPassDesignation->pass_designation;
            } else {
                $designation = $conferenceMemberTypeNameTag?->name_tag ?? null;
            }

            $participant->designation = $designation;

            return $participant;
        });

        if (!$passSetting) {
            return redirect()->back()->with('delete', 'Please Create Pass Setting');
        }
        // dd($registrantsWithDesignation);
        return view('backend.conference.conference-registration.bulk-pass', [
            'registrants' => $registrantsWithDesignation,
            'passSetting' => $passSetting,
            'conference' => $conference,
        ]);
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
        return view('backend.conference.conference-registration.individual-pass', compact('participant', 'passSetting', 'designation', 'conference'));
    }

    public function generateCertificate($society, $conference, ConferenceRegistration $conferenceRegistration)
    {
        return view('backend.conference.conference-registration.generate-certificate');
    }

    public function downloadVoucher($society, $conference, ConferenceRegistration $conferenceRegistration)
    {
        // dd($conferenceRegistration);
        $user = User::where('id', $conferenceRegistration->user_id)->first();
        $date = \Carbon\Carbon::now()->format('F j, Y');
        $conferenceSetting = ConferenceSetting::where('conference_id', $conference->id)->first();
        $conferenceRegistrationAddons = ConferenceRegistration_addon::where('conference_registration_id', $conferenceRegistration->id)->get();
        $workshopRegistraions = WorkshopRegistration::where(['user_id' => $conferenceRegistration->user_id, 'transaction_id' => $conferenceRegistration->transaction_id])->get();
        // dd($workshopRegistraion);
        $membetType = $user->societies->where('id', $conference->society_id)->first()?->pivot?->memberType;
        $memberTypePrice = ConferenceMemberTypePrice::where(['conference_id' => $conference->id, 'member_type_id' => $membetType->id])->first();
        //conference amount
        $conferenceAmount = '';
        if (!empty($conference)) {
            $createdAt = strtotime($conferenceRegistration->created_at);
            $today = strtotime(date('Y-m-d'));

            $earlyBirdDeadline = strtotime($conference->early_bird_registration_deadline);
            $regularDeadline = strtotime($conference->regular_registration_deadline);

            if ($earlyBirdDeadline >= $today && $earlyBirdDeadline >= $createdAt) {
                $conferenceAmount = !empty($memberTypePrice->early_bird_amount) ? $memberTypePrice->early_bird_amount : '';
            } elseif ($regularDeadline >= $today && $regularDeadline >= $createdAt) {
                $conferenceAmount = !empty($memberTypePrice->regular_amount) ? $memberTypePrice->regular_amount : '';
            }
        }


        $addonsData = [];
        foreach ($conferenceRegistrationAddons as $addon) {
            $addonsData[] = [
                'name' => $addon->ConferenceAddon->addon_name,
                'amount' => $addon->amount,
            ];
        }
        // dd($addonsData->toArray());
        $workshopData = [];
        foreach ($workshopRegistraions as $workshop) {
            $workshopData[] = [
                'name' => $workshop->workshop->workshop_title,
                'amount' => $workshop->amount
            ];
        }

        $serviceCharge =  $user->userDetail->country_id != 125 ? $conferenceRegistration->amount * 0.035 : null;
        $accompanyData = null;
        if ($conferenceRegistration->total_attendee > 1) {
            $accompanyData = [
                'accompany_person' => $conferenceRegistration->total_attendee - 1,
                'amount' => $memberTypePrice->guest_amount
            ];
        }

        $data = [
            'namePrefix'      => $user->userDetail->prefix ?? null,
            'conference_theme' => $conference->conference_theme,
            'conference_name' => $conference->conference_name,
            'name'            => $user->fullName($user),
            'namePrefix' => $user->userDetail->namePrefix->prefix,
            'email'           => $user->email,
            'paymentType'     => 'Online Payment',
            'transactionId'   => $conferenceRegistration->transaction_id,
            'amount'          => $conferenceRegistration->amount,
            'amountInWord'    => numberToWord($conferenceRegistration->amount),
            'date'            => $date,
            'societyName'     => $society->users->where('type', 2)->first()->f_name,
            'societyLogo'     => $society->logo,
            'societyPhone'    => $society->phone,
            'societyEmail'    => $society->users->where('type', 2)->first()->email,
            'societyAddress'  => $society->address,
            'primaryColor'    => $conference->primary_color,
            'country'         => $user->userDetail->country_id,
            'signatureName'   => $conferenceSetting->name,
            'signature'       => $conferenceSetting->signature,
            'conferenceAmount' => $conferenceAmount,
            'addons'           => $addonsData,
            'workshop'         => $workshopData,
            'accompany' => $accompanyData,
            'serviceCharge' => $serviceCharge
        ];

        $pdf = Pdf::loadView('emails.conference.payment-voucher', ['data' => $data])
            ->setPaper('legal', 'portrait');

        return $pdf->download('payment-voucher.pdf');
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

    public function destroy($society, $conference, ConferenceRegistration $registrant)
    {
        try {
            if ($registrant->payment_voucher) {
                $this->file_service->deleteFile($registrant->payment_voucher, 'conference/payment-voucher');
            }
            $registrant->delete();
            return redirect()->back()->with('status', 'Registrant Successfully Deleted.');
        } catch (\Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error.');
        }
    }
}
