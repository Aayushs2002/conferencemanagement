<?php

namespace App\Http\Controllers\Backend\Conference;

use App\Exports\ConferenceRegistrationExport;
use App\Exports\ImportLogExport;
use App\Http\Controllers\Controller;
use App\Imports\ConferenceRegistationImport;
use App\Jobs\SendRegistrantEmailJob;
use App\Mail\Conference\CustomRegistrantMail;
use App\Mail\Conference\ExceptionalRegistrationMail;
use App\Mail\Conference\RegistrantAcceptMail;
use App\Mail\Conference\RegistrantRejectMail;
use App\Mail\Conference\RegistrationMail;
use App\Models\Committee\CommitteeMember;
use App\Models\Conference\AccompanyPerson;
use App\Models\Conference\Attendance;
use App\Models\Conference\ConferenceAddon;
use App\Models\Conference\ConferenceMemberTypePrice;
use App\Models\Conference\ConferenceRegistration;
use App\Models\Conference\ConferenceRegistration_addon;
use App\Models\Conference\ConferenceRegistrationKit;
use App\Models\Conference\ConferenceSetting;
use App\Models\Conference\Hall;
use App\Models\Conference\Meal;
use App\Models\Conference\PassSetting;
use App\Models\Conference\Poll;
use App\Models\Conference\UserVote;
use App\Models\ConferenceCommitteePassDesignation;
use App\Models\ConferenceMemberTypeNameTag;
use App\Models\Conference\Submission;
use App\Models\User;
use App\Models\User\ConferenceUserPassDesignation;
use App\Models\User\Department;
use App\Models\User\Designation;
use App\Models\User\Institution;
use App\Models\User\MemberType;
use App\Models\User\NamePrefix;
use App\Models\User\Society;
use App\Models\User\UserDepartment;
use App\Models\User\UserDesignation;
use App\Models\User\UserDetail;
use App\Models\User\UserInstitution;
use App\Models\Workshop\WorkshopRegistration;
use App\Services\File\FileService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

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
                    },
                ]);
            },  
            'conference',
            'accompanyPersons',
            'addons',
        ])
            ->where('conference_id', $conference->id)
            ->where('status', 1);

        if ($request->filled('registrant_type')) {
            $committeeMemberUserIds = \App\Models\Committee\CommitteeMember::where('conference_id', $conference->id)
                ->where('status', 1)
                ->pluck('user_id')
                ->toArray();

            // For Organizer (type 5), include committee members since they get ORG_ IDs.
            // For all other types, explicitly exclude committee members.
            if ($request->registrant_type == 5) {
                $query->where(function ($q) use ($request, $committeeMemberUserIds) {
                    $q->where('registrant_type', $request->registrant_type)
                        ->orWhereIn('user_id', $committeeMemberUserIds);
                });
            } else {
                $query->where('registrant_type', $request->registrant_type)
                    ->where(function ($q) use ($committeeMemberUserIds) {
                        $q->whereNull('user_id');

                        if (! empty($committeeMemberUserIds)) {
                            $q->orWhereNotIn('user_id', $committeeMemberUserIds);
                        } else {
                            $q->orWhereNotNull('user_id');
                        }
                    });
            }
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

        $this->applyCountryScopeFilter($query, $request);

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

        // Get all registrants
        $registrants = $query->get();

        // Separate dummy and real registrants
        $dummyRegistrants = $registrants->whereNull('user_id');
        $realRegistrants = $registrants->whereNotNull('user_id');

        // Apply sorting based on request 
        $sortBy = $request->filled('sort_by') ? $request->sort_by : 'name_asc';
        
        switch ($sortBy) {
            case 'name_asc':
                // Sort alphabetically A-Z
                $realRegistrants = $realRegistrants->sortBy(function ($registrant) {
                    $middleName = !empty($registrant->user->m_name) ? ' ' . $registrant->user->m_name : '';
                    return strtolower($registrant->user->f_name . $middleName . ' ' . $registrant->user->l_name);
                })->values();
                break;
            
            case 'name_desc':
                // Sort alphabetically Z-A
                $realRegistrants = $realRegistrants->sortByDesc(function ($registrant) {
                    $middleName = !empty($registrant->user->m_name) ? ' ' . $registrant->user->m_name : '';
                    return strtolower($registrant->user->f_name . $middleName . ' ' . $registrant->user->l_name);
                })->values();
                break;
            
            case 'latest':
                // Sort by latest registration (newest first)
                $realRegistrants = $realRegistrants->sortByDesc('created_at')->values();
                break;
            
            case 'oldest':
                // Sort by oldest registration (oldest first)
                $realRegistrants = $realRegistrants->sortBy('created_at')->values();
                break;
            
            case 'amount_asc':
                // Sort by amount (low to high)
                $realRegistrants = $realRegistrants->sortBy('amount')->values();
                break;
            
            case 'amount_desc':
                // Sort by amount (high to low)
                $realRegistrants = $realRegistrants->sortByDesc('amount')->values();
                break;
            
            default:
                // Default to alphabetical A-Z
                $realRegistrants = $realRegistrants->sortBy(function ($registrant) {
                    $middleName = !empty($registrant->user->m_name) ? ' ' . $registrant->user->m_name : '';
                    return strtolower($registrant->user->f_name . $middleName . ' ' . $registrant->user->l_name);
                })->values();
                break;
        }

        // Merge: real registrants first (with applied sorting), then dummy registrants
        $registrants = $realRegistrants->merge($dummyRegistrants)->values();

        return view('backend.conference.conference-registration.registrant', [
            'registrants' => $registrants,
            'conference' => $conference,
            'society' => $society,
            'filters' => $request->only(['registrant_type', 'prefix', 'is_invited', 'payment_type', 'from', 'to', 'country_id', 'country_scope', 'member_type_id', 'sort_by']),
        ]);
    }

    public function show(Request $request)
    {
        $registrant = ConferenceRegistration::whereId($request->id)->first();

        return view('backend.conference.conference-registration.view', compact('registrant'));
    }

    public function allPaymentStatuses($society, $conference)
    {
        $paymentStatuses = \App\Models\ConferencePaymentStatus::where('conference_id', $conference->id)
            ->with(['conference', 'user.userDetail.namePrefix', 'user.userDetail.country'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('backend.conference.conference-registration.all-payment-statuses', compact('paymentStatuses', 'society', 'conference'));
    }

    public function showPaymentStatus($society, $conference, ConferenceRegistration $registrant)
    {
        $paymentStatuses = \App\Models\ConferencePaymentStatus::where([
            'conference_id' => $conference->id,
            'user_id' => $registrant->user_id
        ])
        ->with(['conference', 'user'])
        ->orderBy('created_at', 'desc')
        ->get();

        return view('backend.conference.conference-registration.payment-status-page', compact('registrant', 'paymentStatuses', 'society', 'conference'));
    }

    public function edit($society, $conference, ConferenceRegistration $registrant)
    {
        // $prefixesAll = NamePrefix::whereStatus(1)->get();
        // if ($society && $society->namePrefixes()->exists()) {
        //     $prefixesAll = $society->namePrefixes()->where('status', 1)->get();
        //     // dd($prefixesAll);
        // } else {
        //     // Fallback to all active prefixes if society hasn't selected any
        //     $prefixesAll = NamePrefix::whereStatus(1)->get();
        // }
        $loadData = function ($relation, $model) use ($society) {
            if ($society && $society->$relation()->exists()) {
                return $society->$relation()->where('status', 1)->get();
            }

            return $model::where('status', 1)->get();
        };

        $institutions = $loadData('institutions', Institution::class);
        // dd($institutions);
        $designations = $loadData('designations', Designation::class);
        $departments = $loadData('departments', Department::class);
        $prefixesAll = $loadData('namePrefixes', NamePrefix::class);
        $conferenceAddons = ConferenceAddon::where('conference_id', $conference->id)->get();
        $memberTypes = MemberType::where(['society_id' => $society->id, 'status' => 1])->get();
        $countries = \App\Models\User\Country::where('status', 1)->get();

        // Get users for linking (only if registration has no user)
        $users = collect();
        if (empty($registrant->user_id)) {
            // Get all society members who are not already registered for this conference
            $alreadyRegisteredUserIds = ConferenceRegistration::where('conference_id', $conference->id)
                ->where('status', 1)
                ->whereNotNull('user_id')
                ->pluck('user_id')
                ->toArray();

            $users = $society->users()
                ->whereNotIn('users.id', $alreadyRegisteredUserIds)
                ->where('users.status', 1)
                ->get();
        }

        // Check for custom institution, designation, department
        $userInstitution = UserInstitution::where('user_id', $registrant->user_id)->first();
        $userDesignation = UserDesignation::where('user_id', $registrant->user_id)->first();
        $userDepartment = UserDepartment::where('user_id', $registrant->user_id)->first();

        // $institutions = \App\Models\User\Institution::where('status', 1)->get();
        // $designations = \App\Models\User\Designation::where('status', 1)->get();
        // $departments = \App\Models\User\Department::where('status', 1)->get();

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
            'users',
            'userInstitution',
            'userDesignation',
            'userDepartment'
        ));
    }

    public function update(Request $request, $society, $conference, ConferenceRegistration $registrant)
    {
        try {
            $rules = [
                'registrant_type' => 'required',
                'registration_id' => [
                    'nullable',
                    'string',
                    'max:255',
                    Rule::unique('conference_registrations', 'registration_id')
                        ->ignore($registrant->id)
                        ->where(function ($query) use ($conference) {
                            return $query->where('conference_id', $conference->id);
                        }),
                ],
                'additional_guests' => 'nullable|numeric',
                'meal_type' => 'required',
                'payment_type' => 'required|in:1,2,3,4,5,6',
                'payment_voucher' => 'nullable|mimes:jpg,png,pdf|max:250',
                'transaction_id' => 'required|unique:conference_registrations,transaction_id,'.$registrant->id,
                'amount' => 'required|numeric',
            ];

            // Handle user linking or creation for dummy registrations
            if (empty($registrant->user_id)) {
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
                $rules['designation_id'] = 'nullable';
                $rules['department_id'] = 'nullable';
                $rules['institution_id'] = 'nullable';
                $rules['address'] = 'nullable';
                $rules['member_type_id'] = 'required';
                $rules['email'] = 'required|email|unique:users,email,'.$registrant->user_id;
                $rules['country_id'] = 'required';
                $rules['council_number'] = 'nullable';
            }

            if ($request->institution_id == 'other') {
                $rules['other_institution_name'] = 'required';
            }

            if ($request->designation_id == 'other') {
                $rules['other_designation'] = 'required';
            }

            if ($request->department_id == 'other') {
                $rules['other_department'] = 'required';
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

            // Handle "other" options before updating user
            if ($request->institution_id == 'other') {
                unset($validated['institution_id']);
            }
            if ($request->designation_id == 'other') {
                unset($validated['designation_id']);
            }
            if ($request->department_id == 'other') {
                unset($validated['department_id']);
            }

            if (empty($validated['additional_guests'])) {
                $validated['total_attendee'] = 1;
            } else {
                $validated['total_attendee'] = $validated['additional_guests'] + 1;
            }

            if (! $request->filled('registration_id')) {
                unset($validated['registration_id']);
            }

            if (! empty($validated['payment_voucher'])) {
                // Delete old voucher if exists
                if ($registrant->payment_voucher) {
                    $this->file_service->deleteFile($registrant->payment_voucher, 'conference/payment-voucher');
                }
                $validated['payment_voucher'] = $this->file_service->fileUpload($validated['payment_voucher'], 'payment_voucher', 'conference/payment-voucher');
            }

            DB::beginTransaction();

            // Handle user linking or creation
            if (empty($registrant->user_id)) {
                if ($request->filled('existing_user_id')) {
                    // Check if user is already registered for this conference
                    $existingRegistration = ConferenceRegistration::where('conference_id', $conference->id)
                        ->where('user_id', $validated['existing_user_id'])
                        ->where('status', 1)
                        ->where('id', '!=', $registrant->id)
                        ->first();

                    if ($existingRegistration) {
                        DB::rollBack();

                        return redirect()->back()->withInput()->with('delete', 'This user is already registered for this conference.');
                    }

                    // Link to existing user
                    $registrant->user_id = $validated['existing_user_id'];
                    $user = User::findOrFail($validated['existing_user_id']);
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

                    $registrant->user_id = $newUser->id;
                    $user = $newUser;
                }
            } else {
                // Update existing user
                $user = User::findOrFail($registrant->user_id);
                $user->update([
                    'f_name' => $validated['f_name'],
                    'm_name' => $validated['m_name'],
                    'l_name' => $validated['l_name'],
                    'email' => $validated['email'],
                ]);

                // Update user details
                $user->userDetail->update([
                    'phone' => $validated['phone'],
                    'gender' => $validated['gender'],
                    'name_prefix_id' => $validated['name_prefix_id'],
                    'designation_id' => $validated['designation_id'] ?? null,
                    'department_id' => $validated['department_id'] ?? null,
                    'institution_id' => $validated['institution_id'] ?? null,
                    'institute_address' => $validated['address'],
                    'council_number' => $validated['council_number'],
                    'country_id' => $validated['country_id'],
                ]);

                // Create custom institution, designation, department if "other" was selected
                if ($request->institution_id == 'other') {
                    // Delete existing custom institution for this user
                    UserInstitution::where('user_id', $user->id)->delete();
                    UserInstitution::create([
                        'user_id' => $user->id,
                        'institution_name' => $request->other_institution_name,
                    ]);
                }
                if ($request->designation_id == 'other') {
                    // Delete existing custom designation for this user
                    UserDesignation::where('user_id', $user->id)->delete();
                    UserDesignation::create([
                        'user_id' => $user->id,
                        'designation_name' => $request->other_designation,
                    ]);
                }
                if ($request->department_id == 'other') {
                    // Delete existing custom department for this user
                    UserDepartment::where('user_id', $user->id)->delete();
                    UserDepartment::create([
                        'user_id' => $user->id,
                        'department_name' => $request->other_department,
                    ]);
                }

                // Update user society membership
                $user->societies()->syncWithoutDetaching([
                    $society->id => ['member_type_id' => $validated['member_type_id']],
                ]);
            }

            // Update conference registration
            $registrant->update([
                'registrant_type' => $validated['registrant_type'],
                'registration_id' => $validated['registration_id'] ?? $registrant->registration_id,
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

            $middleName = ! empty($user->m_name) ? $user->m_name.' ' : '';
            logActivity($conference->id, 'Updated Conference Registration', $user->f_name.' '.$middleName.$user->l_name.' registration updated' . ($user->userDetail->country ? ' from '.$user->userDetail->country->country_name : ''));

            DB::commit();

            return redirect()->route('conference.conference-registration.index', [$society, $conference])
                ->with('status', 'Conference registration updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('delete', 'Failed to update registration: '.$e->getMessage());
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
                    'message' => 'Payment voucher deleted successfully.',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No voucher found to delete.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete voucher: '.$e->getMessage(),
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
                'total_attendee' => $activeAccompanyCount + 1, // +1 for the registrant
            ]);

            logActivity($conference->id, 'Deleted Accompany Person', 'Deleted accompany person: '.$personName.' from '.$registration->user?->fullName($registration->user).' registration');

            return response()->json([
                'success' => true,
                'message' => 'Accompany person deleted successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete accompany person: '.$e->getMessage(),
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
                'message' => 'Import failed: '.$e->getMessage(),
            ], 500);
        }

        if (! empty($import->log)) {
            $fileName = 'import_skipped_log_'.now()->format('Y_m_d_H_i_s').'.xlsx';

            Excel::store(new ImportLogExport($import->log), $fileName, 'public_uploads');

            return response()->json([
                'type' => 'log',
                'file' => url($fileName),
                'message' => 'Some rows were skipped, download the log file.',
            ]);
        }

        return response()->json([
            'type' => 'success',
            'message' => 'Excel file imported successfully.',
        ]);
    }

    public function registerForExceptionalCase($society, $conference)
    {
        $registeredUserIds = ConferenceRegistration::where('conference_id', $conference->id)
            ->whereNotNull('user_id')
            ->pluck('user_id');
        $society = Society::with(['users' => function ($query) use ($registeredUserIds) {
            $query->where('type', 3)
                ->whereNotIn('users.id', $registeredUserIds)
                ->orderByDesc('users.id');
        }])->where([
            'id' => $conference->society_id,
            'status' => 1,
        ])->first();
        $users = $society ? $society->users : collect();

        return view('backend.conference.conference-registration.register-for-exceptional-case', compact('users', 'society', 'conference'));
    }

    public function getUserMemberTypeAddons(Request $request, $society, $conference)
    {
        try {
            $userId = $request->user_id;

            if (! $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID is required',
                ]);
            }

            // Get user's member type for this society
            $user = User::find($userId);
            $userSociety = $user->societies->where('id', $conference->society_id)->first();
            $memberType = $userSociety?->pivot?->memberType;

            if (! $memberType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member type not found for this user',
                ]);
            }

            // Get addons for this member type
            $addons = ConferenceAddon::where([
                'conference_id' => $conference->id,
                'member_type_id' => $memberType->id,
                'status' => 1,
            ])
                ->select('id', 'addon_name', 'early_bird_amount', 'regular_amount', 'on_site_amount', 'guest_amount')
                ->get()
                ->map(function ($addon) use ($conference) {
                    // Determine which amount to use based on registration period
                    $amount = $addon->on_site_amount;
                    if ($conference->early_bird_registration_deadline >= date('Y-m-d')) {
                        $amount = $addon->early_bird_amount ?? $addon->on_site_amount;
                    } elseif ($conference->regular_registration_deadline >= date('Y-m-d')) {
                        $amount = $addon->regular_amount ?? $addon->on_site_amount;
                    }

                    return [
                        'id' => $addon->id,
                        'addon_name' => $addon->addon_name,
                        'amount' => $amount,
                        'guest_amount' => $addon->guest_amount ?? $amount,
                    ];
                });

            return response()->json([
                'success' => true,
                'addons' => $addons,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching addons: '.$e->getMessage(),
            ]);
        }
    }

    public function getMemberTypeAddons(Request $request, $society, $conference)
    {
        try {
            $memberTypeId = $request->member_type_id;

            if (! $memberTypeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member type ID is required',
                ]);
            }

            // Get addons for this member type
            $addons = ConferenceAddon::where([
                'conference_id' => $conference->id,
                'member_type_id' => $memberTypeId,
                'status' => 1,
            ])
                ->select('id', 'addon_name', 'early_bird_amount', 'regular_amount', 'on_site_amount', 'guest_amount')
                ->get()
                ->map(function ($addon) use ($conference) {
                    // Determine which amount to use based on registration period
                    $amount = $addon->on_site_amount;
                    if ($conference->early_bird_registration_deadline >= date('Y-m-d')) {
                        $amount = $addon->early_bird_amount ?? $addon->on_site_amount;
                    } elseif ($conference->regular_registration_deadline >= date('Y-m-d')) {
                        $amount = $addon->regular_amount ?? $addon->on_site_amount;
                    }

                    return [
                        'id' => $addon->id,
                        'addon_name' => $addon->addon_name,
                        'amount' => $amount,
                        'guest_amount' => $addon->guest_amount ?? $amount,
                    ];
                });

            return response()->json([
                'success' => true,
                'addons' => $addons,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching addons: '.$e->getMessage(),
            ]);
        }
    }

    public function registerForExceptionalCaseSubmit(Request $request, $society, $conference)
    {
        // dd($request->all());
        try {
            $rules = [
                'user_id' => 'required',
                'registrant_type' => 'required',
                'payment_status' => 'required|in:paid,unpaid',
                'transaction_id' => 'nullable|unique:conference_registrations,transaction_id',
                'amount' => 'required|numeric',
                'meal_type' => 'required',
                'additional_guests' => 'nullable|numeric',
                'payment_voucher' => 'nullable|mimes:jpg,png,pdf|max:250',
            ];

            if ($request->payment_status === 'paid') {
                $rules['transaction_id'] = 'required|unique:conference_registrations,transaction_id';
            }

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

            if ($validated['payment_status'] === 'unpaid' && empty($validated['transaction_id'])) {
                $validated['transaction_id'] = 'CREDIT-'.now()->format('YmdHis').'-'.mt_rand(1000, 9999);
            }

            // for values start

            if (empty($validated['additional_guests'])) {
                $validated['total_attendee'] = 1;
            } else {
                $validated['total_attendee'] = $validated['additional_guests'] + 1;
            }
            $validated['conference_id'] = $conference->id;
            $validated['token'] = random_word(60);
            $validated['verified_status'] = $validated['payment_status'] === 'paid' ? 1 : 0;
            $validated['payment_type'] = $validated['payment_status'] === 'paid' ? 6 : 9;
            $date = \Carbon\Carbon::now()->format('F j, Y');

            if (! empty($validated['payment_voucher'])) {

                $validated['payment_voucher'] = $this->file_service->fileUpload($validated['payment_voucher'], 'payment_voucher', 'conference/payment-voucher');
            }

            // for values end
            $conferenceSetting = ConferenceSetting::where('conference_id', $conference->id)->first();

            $user = User::whereId($validated['user_id'])->first();
            $mailData = [
                'namePrefix' => $user->userDetail->prefix ?? null,
                'conference_theme' => $conference->conference_theme,
                'conference_name' => $conference->conference_name,
                'name' => $user->fullName($user),
                'namePrefix' => $user->userDetail->namePrefix?->prefix,
                'email' => $user->email,
                'paymentType' => $validated['payment_status'] === 'paid' ? 'Online Payment' : 'Credit',
                'transactionId' => $validated['transaction_id'],
                'amount' => $validated['amount'],
                'amountInWord' => numberToWord(abs((int) $validated['amount'])),
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
                'addons' => [],
                'workshop' => [],
                'accompany' => null,
                'serviceCharge' => null,
                'is_unpaid' => $validated['payment_status'] === 'unpaid',
                'due_or_credit_amount' => (float) $validated['amount'],
                'payment_link' => route('my-society.conference.index', [$society, $conference]),
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
            // if ($request->conference_addon_id) {
            //     foreach ($request->conference_addon_id as $addon_id) {
            //         $addon = ConferenceAddon::where('id', $request->conference_addon_id)->first();
            //         // dd($addon);
            //         ConferenceRegistration_addon::create([
            //             'conference_registration_id' => $registration->id,
            //             'conference_addon_id' => $addon_id,
            //             'amount' => $user->userDetail->country_id == 125 ? $addon->addon_national_amount : $addon->addon_international_amount,
            //         ]);
            //     }
            // }
            if (! empty($request->selected_addons)) {

                $addons = explode(',', $request->selected_addons);
                $insertData = [];

                // dd($request->selected_addons);
                foreach ($addons as $addon) {
                    $parts = explode(':', $addon);
                    $addonId = $parts[0];
                    $amount = $parts[1]; // Main attendee amount
                    $guestAmount = isset($parts[2]) ? $parts[2] : $parts[1]; // Guest amount
                    $includeGuest = isset($parts[3]) && $request->additional_guests >= 1 && $parts[3] == '1' ? 1 : 0;

                    $insertData[] = [
                        'conference_registration_id' => $registration->id,
                        'conference_addon_id' => $addonId,
                        'amount' => $amount,
                        'include_for_guests' => $includeGuest,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                ConferenceRegistration_addon::insert($insertData);
            }
            logActivity($conference->id, 'Registered Conference', $user->fullName($user).' is registered to conference'.($user->userDetail->country->country_name ? ' from '.$user->userDetail->country->country_name : ''));

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

            logActivity($registration->conference_id, 'Add Person', 'Added '.$validated['additional_guests'].' Guests to '.$registration->user?->fullName($registration->user).' is registered to conference');

            $type = 'success';
            $message = 'Successfully Added';

            DB::commit();

            // return redirect()->back()->with('status', 'Successfully registered.');
        } catch (Exception $e) {
            DB::rollBack();
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
            $message = 'Registrant type Converted Successfully Added';

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
                $message = 'Attendee Updated Successfully';
            } else {
                $message = 'Presenter Updated Successfully';
            }
        } catch (Exception $e) {
            $type = 'error';
            $message = $e->getMessage();
        }

        return response()->json(['type' => $type, 'message' => $message]);
    }

    public function registrationOrInvitation($society, $conference)
    {
        $loadData = function ($relation, $model) use ($society) {
            if ($society && $society->$relation()->exists()) {
                return $society->$relation()->where('status', 1)->get();
            }

            return $model::where('status', 1)->get();
        };

        $institutions = $loadData('institutions', Institution::class);
        $designations = $loadData('designations', Designation::class);
        $departments = $loadData('departments', Department::class);
        $prefixesAll = $loadData('namePrefixes', NamePrefix::class);

        return view('backend.conference.conference-registration.registration-or-invitation', compact('prefixesAll', 'society', 'conference', 'institutions', 'designations', 'departments'));
    }

    public function registrationOrInvitationSubmit(Request $request, $society, $conference)
    {
        try {
            // dd($request->all());
            $isInvitedGuest = $request->boolean('invited_guest');

            $checkUser = User::whereEmail($request->email)->first();
            $conferenceRegistration = ConferenceRegistration::where(['conference_id' => $conference->id, 'user_id' => $checkUser?->id, 'status' => 1])->first();
            if ($conferenceRegistration && $checkUser) {
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
                'payment_status' => $isInvitedGuest ? 'nullable|in:paid,unpaid' : 'required|in:paid,unpaid',
                'payment_voucher' => $isInvitedGuest ? 'nullable' : 'nullable|mimes:jpg,png,pdf|max:250',
                'email' => 'required|email|unique:users,email',
            ];

            if ($request->institution_id == 'other') {
                $rules['other_institution_name'] = 'required';
            }

            if ($request->designation_id == 'other') {
                $rules['other_designation'] = 'required';
            }

            if ($request->department_id == 'other') {
                $rules['other_department'] = 'required';
            }

            $rules['council_number'] = 'nullable';
            $rules['amount'] = $isInvitedGuest ? 'nullable|numeric' : 'required|numeric';
            $rules['transaction_id'] = $isInvitedGuest ? 'nullable|unique:conference_registrations,transaction_id' : 'required|unique:conference_registrations,transaction_id';

            if (! $isInvitedGuest && $request->payment_status === 'paid') {
                $rules['transaction_id'] = 'required|unique:conference_registrations,transaction_id';
            }

            if ($request->registrant_type == 2) {
                $rules['short_cv'] = 'required';
            }

            if ($request->additional_guests >= 1) {
                $rules['person_name.*'] = 'required';
            }

            $message = [
                'transaction_id.unique' => 'Transaction/Reference Id already exist.',
                'person_name.*.required' => 'Each person name is required.',
            ];

            $validated = $request->validate($rules, $message);

            if ($isInvitedGuest) {
                $validated['payment_status'] = 'unpaid';
                $validated['amount'] = 0;
                $validated['transaction_id'] = null;
                unset($validated['payment_voucher']);
            } elseif ($validated['payment_status'] === 'unpaid' && empty($validated['transaction_id'])) {
                $validated['transaction_id'] = 'CREDIT-'.now()->format('YmdHis').'-'.mt_rand(1000, 9999);
            }

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
            $validated['verified_status'] = $validated['payment_status'] === 'paid' ? 1 : 0;
            $validated['payment_type'] = $validated['payment_status'] === 'paid' ? 6 : 9;
            $validated['invitation_response_token'] = $invitationToken;

            $date = \Carbon\Carbon::now()->format('F j, Y');

            if (! empty($validated['payment_voucher'])) {
                $validated['payment_voucher'] = $this->file_service->fileUpload($validated['payment_voucher'], 'payment_voucher', 'conference/payment-voucher');
            }
            $conferenceSetting = ConferenceSetting::where('conference_id', $conference->id)->first();

            // for values end

            // Prepare addon data for email
            $addonData = [];
            // if (!empty($request->selected_addons)) {

            //     $addons = explode(',', $request->selected_addons);
            //     foreach ($addons as $addon) {
            //         // dd($addon);
            //         $parts = explode(':', $addon);
            //         $addonId = $parts[0];
            //         $mainAmount = $parts[1];
            //         $guestAmount = isset($parts[2]) ? $parts[2] : $parts[1]; // Use main amount if guest not specified
            //         $includeGuest = isset($parts[3]) ? $parts[3] : '1'; // Default to include guest

            //         $addonDetail = ConferenceAddon::find($addonId);
            //         $addonData[] = [
            //             'name'   => $addonDetail->addon_name ?? 'Addon ' . $addonId,
            //             'amount' => $mainAmount,
            //             'guest_amount' => $guestAmount,
            //             'include_guest' => $includeGuest == '1',
            //             'quantity' => $validated['total_attendee'] ?? 1

            //         ];
            //     }
            // }

            // Prepare accompany person data for email
            $accompanyData = null;
            if (!empty($validated['additional_guests']) && $validated['additional_guests'] >= 1) {
                // Get member type price for guest amount
                $memberTypePrice = ConferenceMemberTypePrice::where([
                    'conference_id' => $conference->id,
                    'member_type_id' => $validated['member_type_id']
                ])->first();

                $accompanyData = [
                    'accompany_person' => $validated['additional_guests'],
                    'amount' => $memberTypePrice ? $memberTypePrice->guest_amount : 0,
                ];
            }

            $middleName = ! empty($validated['m_name']) ? $validated['m_name'].' ' : '';
            $namePrefix = DB::table('name_prefixes')->whereId($validated['name_prefix_id'])->first()->prefix;
            $data = [
                'namePrefix' => $namePrefix,
                'name' => $validated['f_name'].' '.$middleName.$validated['l_name'],
                'email' => $validated['email'],
                'password' => $password,
                'conference_theme' => $conference->conference_theme,
                'conference_name' => $conference->conference_name,
                'paymentType' => $isInvitedGuest ? 'Invitation' : ($validated['payment_status'] === 'paid' ? 'Online Payment' : 'Credit'),
                'transactionId' => $isInvitedGuest ? null : $validated['transaction_id'],
                'amount' => $validated['amount'],
                'amountInWord' => numberToWord(abs((int) $validated['amount'])),
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
                'addons' => $addonData,
                'workshop' => [],
                'accompany' => $accompanyData,
                'serviceCharge' => null,
                'invitationType' => 1,
                'is_invited' => $isInvitedGuest ? 1 : 0,
                'invitation_token' => $invitationToken,
                'invitation_url' => route('invitation.show', $invitationToken),
                'is_unpaid' => $validated['payment_status'] === 'unpaid',
                'due_or_credit_amount' => (float) $validated['amount'],
                'payment_link' => $isInvitedGuest ? null : route('my-society.conference.index', [$society, $conference]),
            ];
            Mail::to($validated['email'])->send(new RegistrationMail($data, $conference->conference_name));

            if ($isInvitedGuest) {
                $validated['is_invited'] = 1;
            }

            unset($validated['delegate']);
            DB::beginTransaction();

            // Handle "other" options before creating user
            if ($request->institution_id == 'other') {
                unset($validated['institution_id']);
            }

            if ($request->designation_id == 'other') {
                unset($validated['designation_id']);
            }

            if ($request->department_id == 'other') {
                unset($validated['department_id']);
            }

            // insert table-1
            $validated['type'] = 3;
            $storeUser = User::create($validated);

            $validated['user_id'] = $storeUser->id;

            // insert table-2
            UserDetail::create($validated);

            // Create custom institution, designation, department if "other" was selected
            if ($request->institution_id == 'other') {
                UserInstitution::create([
                    'user_id' => $storeUser->id,
                    'institution_name' => $request->other_institution_name,
                ]);
            }

            if ($request->designation_id == 'other') {
                UserDesignation::create([
                    'user_id' => $storeUser->id,
                    'designation_name' => $request->other_designation,
                ]);
            }

            if ($request->department_id == 'other') {
                UserDepartment::create([
                    'user_id' => $storeUser->id,
                    'department_name' => $request->other_department,
                ]);
            }

            // $societyId = current_user()->societies->value('id');
            // insert table-3
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

            // Store addons with proper format: addonId:mainAmount:guestAmount:includeGuest
            if (! empty($request->selected_addons)) {
                $addons = explode(',', $request->selected_addons);
                $insertData = [];

                foreach ($addons as $addon) {
                    $parts = explode(':', $addon);
                    $addonId = $parts[0];
                    $amount = $parts[1]; // Main attendee amount
                    $guestAmount = isset($parts[2]) ? $parts[2] : $parts[1]; // Guest amount
                    $includeGuest = isset($parts[3]) && $request->additional_guests >= 1 && $parts[3] == '1' ? 1 : 0;

                    $insertData[] = [
                        'conference_registration_id' => $registration->id,
                        'conference_addon_id' => $addonId,
                        'amount' => $amount,
                        'include_for_guests' => $includeGuest,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                ConferenceRegistration_addon::insert($insertData);
            }
            logActivity($conference->id, $request->has('invited_guest') ? 'Invited Conference' : 'Registered Conference', $validated['f_name'].' '.$middleName.$validated['l_name'].' is registered to conference');
            DB::commit();

            return redirect()->back()->with('status', 'Successfully registered.');
        } catch (Exception $e) {
            // dd($e->getMessage());
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
            'user.userDetail',
        ])
            ->where('conference_id', $conference->id)
            ->where('status', 1);

        if ($request->filled('registrant_type')) {
            $committeeMemberUserIds = \App\Models\Committee\CommitteeMember::where('conference_id', $conference->id)
                ->where('status', 1)
                ->pluck('user_id')
                ->toArray();

            // For Organizer (type 5), include committee members since they get ORG_ IDs.
            // For all other types, explicitly exclude committee members.
            if ($request->registrant_type == 5) {
                $query->where(function ($q) use ($request, $committeeMemberUserIds) {
                    $q->where('registrant_type', $request->registrant_type)
                        ->orWhereIn('user_id', $committeeMemberUserIds);
                });
            } else {
                $query->where('registrant_type', $request->registrant_type)
                    ->where(function ($q) use ($committeeMemberUserIds) {
                        $q->whereNull('user_id');

                        if (! empty($committeeMemberUserIds)) {
                            $q->orWhereNotIn('user_id', $committeeMemberUserIds);
                        } else {
                            $q->orWhereNotNull('user_id');
                        }
                    });
            }
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

        $this->applyCountryScopeFilter($query, $request);

        if ($request->filled('prefix')) {
            $query->whereHas('user.userDetail', function ($q) use ($request) {
                $q->where('name_prefix_id', $request->prefix);
            });
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        // Get all registrants
        $registrants = $query->get();

        // Separate dummy and real registrants
        $dummyRegistrants = $registrants->whereNull('user_id');
        $realRegistrants = $registrants->whereNotNull('user_id');

        // Sort real registrants alphabetically by user's full name in ascending order
        $realRegistrants = $realRegistrants->sortBy(function ($registrant) {
            $middleName = !empty($registrant->user->m_name) ? ' ' . $registrant->user->m_name : '';
            return strtolower($registrant->user->f_name . $middleName . ' ' . $registrant->user->l_name);
        })->values();

        // Merge: real registrants first (alphabetically), then dummy registrants
        $registrants = $realRegistrants->merge($dummyRegistrants)->values();

        return Excel::download(new ConferenceRegistrationExport($registrants), 'conferenceRegistration.xlsx');
    }

    public function generatePass(Request $request, $society, $conference)
    {
        // Increase memory and execution time limits for large datasets
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', '300');
        set_time_limit(300);

        $society_id = $society->id ?? null;

        $query = ConferenceRegistration::with([
            'user.societies' => function ($query) use ($society_id) {
                $query->where('society_id', $society_id);
            },
            'user.userDetail', 
        ])
            ->where('conference_id', $conference->id) 
            ->where('status', 1);

        if ($request->filled('registrant_type')) {
            $committeeMemberUserIds = \App\Models\Committee\CommitteeMember::where('conference_id', $conference->id)
                ->where('status', 1)
                ->pluck('user_id')
                ->toArray();

            // For Organizer (type 5), include committee members since they get ORG_ IDs.
            // For all other types, explicitly exclude committee members.
            if ($request->registrant_type == 5) {
                $query->where(function ($q) use ($request, $committeeMemberUserIds) {
                    $q->where('registrant_type', $request->registrant_type)
                        ->orWhereIn('user_id', $committeeMemberUserIds);
                });
            } else {
                $query->where('registrant_type', $request->registrant_type)
                    ->where(function ($q) use ($committeeMemberUserIds) {
                        $q->whereNull('user_id');

                        if (! empty($committeeMemberUserIds)) {
                            $q->orWhereNotIn('user_id', $committeeMemberUserIds);
                        } else {
                            $q->orWhereNotNull('user_id');
                        }
                    });
            }
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

        $this->applyCountryScopeFilter($query, $request);

        if ($request->filled('prefix')) {
            $query->whereHas('user.userDetail', function ($q) use ($request) {
                $q->where('name_prefix_id', $request->prefix);
            });
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
 
        // Get all registrants
        $registrants = $query->get();

        // Separate dummy and real registrants
        $dummyRegistrants = $registrants->whereNull('user_id');
        $realRegistrants = $registrants->whereNotNull('user_id');

        // Sort real registrants alphabetically by user's full name in ascending order
        $realRegistrants = $realRegistrants->sortBy(function ($registrant) {
            $middleName = !empty($registrant->user->m_name) ? ' ' . $registrant->user->m_name : '';
            return strtolower($registrant->user->f_name . $middleName . ' ' . $registrant->user->l_name);
        })->values(); 

        // Merge: real registrants first (alphabetically), then dummy registrants
        $registrants = $realRegistrants->merge($dummyRegistrants)->values();

        $passSetting = PassSetting::where(['conference_id' => $conference->id, 'status' => 1])->first();

        $registrantsWithDesignation = $registrants->map(function ($participant) use ($conference, $passSetting) {
            $designation = null;
            $color = null;
            $designationCountryName = null;

            $userSociety = $participant->user?->societies->first();
            $memberType = $userSociety?->pivot?->memberType;

            $conferenceUserPassDesignation = ConferenceUserPassDesignation::where([
                'conference_id' => $conference->id,
                'user_id' => $participant->user_id,
            ])->first();

            $conferenceMemberTypeNameTag = null;
            if ($memberType) {
                $conferenceMemberTypeNameTag = ConferenceMemberTypeNameTag::where([
                    'conference_id' => $conference->id,
                    'member_type_id' => $memberType->id,
                    'registrant_type' => $participant->registrant_type,
                ])->first();
            }

            // Check if user is a committee member
            $committeeMember = CommitteeMember::where([
                'conference_id' => $conference->id,
                'user_id' => $participant->user_id,
                'status' => 1,
            ])->first();
            // dd($committeeMember);
            $conferenceCommitteePassDesignation = null;
            if ($committeeMember) {
                $conferenceCommitteePassDesignation = ConferenceCommitteePassDesignation::where([
                    'conference_id' => $conference->id,
                    'committee_id' => $committeeMember->committee_id,
                    'designation_id' => $committeeMember->designation_id,
                ])->first();
                // dd($conferenceCommitteePassDesignation);
            }

            // Priority: ConferenceUserPassDesignation > Committee Designation > Workshop Designation > ConferenceMemberTypeNameTag
            if ($conferenceUserPassDesignation) {
                $designation = $conferenceUserPassDesignation->pass_designation;
                $color = $conferenceUserPassDesignation->color;
            } elseif ($conferenceCommitteePassDesignation) {
                $designation = $conferenceCommitteePassDesignation->name_tag;
                $color = $conferenceCommitteePassDesignation->color;
            } else {
                // Check if user is registered for any workshop
                $workshopRegistration = \App\Models\Workshop\WorkshopRegistration::where([
                    'user_id' => $participant->user_id ? $participant->user_id : 0,
                    'status' => 1,
                ])->first();
                if ($workshopRegistration && $passSetting) {
                    // registrant_type: 1 = participant, 2 = trainer
                    if ($workshopRegistration->registrant_type == 2 && ! empty($passSetting->workshop_trainer_name_tag)) {
                        $designation = $passSetting->workshop_trainer_name_tag;
                        $color = $passSetting->workshop_trainer_color ?? '#7367f0';
                    } elseif ($workshopRegistration->registrant_type == 1 && ! empty($passSetting->workshop_participant_name_tag)) {
                        $designation = $passSetting->workshop_participant_name_tag;
                        $color = $passSetting->workshop_participant_color ?? '#7367f0';
                    } else {
                        // Fall through to next priority
                        $workshopRegistration = null;
                    }
                }

                // If no workshop designation found, check member type name tag
                if (! isset($designation) && $conferenceMemberTypeNameTag) {
                    $designation = $conferenceMemberTypeNameTag->name_tag;
                    $color = $conferenceMemberTypeNameTag->color;

                    if ((int) ($passSetting?->include_country_for_international ?? 0) === 1 &&
                        $participant->isInternationalParticipant()) {
                        $designationCountryName = $participant->user?->userDetail?->country?->country_name;
                    }
                } elseif (! isset($designation)) {
                    // Fallback: Try to get any name tag for this registrant type (ignore member_type)
                    $fallbackNameTag = ConferenceMemberTypeNameTag::where('conference_id', $conference->id)
                        ->where('registrant_type', $participant->registrant_type)
                        ->first();

                    if ($fallbackNameTag) {
                        $designation = $fallbackNameTag->name_tag;
                        $color = $fallbackNameTag->color ?? '#7367f0';

                        if ((int) ($passSetting?->include_country_for_international ?? 0) === 1 &&
                            $participant->isInternationalParticipant()) {
                            $designationCountryName = $participant->user?->userDetail?->country?->country_name;
                        }
                    } else {
                        // Ultimate fallback based on registrant type
                        $registrantTypes = [
                            1 => 'Attendee',
                            2 => 'Speaker/Presenter',
                            3 => 'Session Chair',
                            4 => 'Special Guest',
                            5 => 'Organizer',
                        ];
                        $designation = $registrantTypes[$participant->registrant_type] ?? 'Participant';
                        $color = '#7367f0';
                    }
                }
            }

            $participant->designation = $designation;
            $participant->designation_color = $color;
            $participant->designation_country_name = $designationCountryName;

            return $participant;
        });

        if (! $passSetting) {
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
        $designationCountryName = null;
        $userSociety = $participant->user?->societies->first();
        $memberType = $userSociety?->pivot?->memberType;
        $conferenceUserPassDesignation = ConferenceUserPassDesignation::where(['conference_id' => $conference->id, 'user_id' => $participant->user_id])->first();

        $conferenceMemberTypeNameTag = null;
        if ($memberType) {
            $conferenceMemberTypeNameTag = ConferenceMemberTypeNameTag::where([
                'conference_id' => $conference->id,
                'member_type_id' => $memberType->id,
                'registrant_type' => $participant->registrant_type,
            ])->first();
        }

        // Check if user is a committee member
        $committeeMember = CommitteeMember::where([
            'conference_id' => $conference->id,
            'user_id' => $participant->user_id,
            'status' => 1,
        ])->first();

        $conferenceCommitteePassDesignation = null;
        if ($committeeMember) {
            $conferenceCommitteePassDesignation = ConferenceCommitteePassDesignation::where([
                'conference_id' => $conference->id,
                'committee_id' => $committeeMember->committee_id,
                'designation_id' => $committeeMember->designation_id,
            ])->first();
        }

        // Priority: ConferenceUserPassDesignation > Committee Designation > Workshop Designation > ConferenceMemberTypeNameTag
        if ($conferenceUserPassDesignation) {
            $designation = $conferenceUserPassDesignation->pass_designation;
            $color = $conferenceUserPassDesignation->color;
        } elseif ($conferenceCommitteePassDesignation) {
            $designation = $conferenceCommitteePassDesignation->name_tag;
            $color = $conferenceCommitteePassDesignation->color;
        } else {
            // Check if user is registered for any workshop
            $workshopRegistration = \App\Models\Workshop\WorkshopRegistration::where([
                'user_id' => $participant->user_id ? $participant->user_id : 0,
                'status' => 1,
            ])->first();

            if ($workshopRegistration && $passSetting) {
                // registrant_type: 1 = participant, 2 = trainer
                if ($workshopRegistration->registrant_type == 2 && ! empty($passSetting->workshop_trainer_name_tag)) {
                    $designation = $passSetting->workshop_trainer_name_tag;
                    $color = $passSetting->workshop_trainer_color ?? '#7367f0';
                } elseif ($workshopRegistration->registrant_type == 1 && ! empty($passSetting->workshop_participant_name_tag)) {
                    $designation = $passSetting->workshop_participant_name_tag;
                    $color = $passSetting->workshop_participant_color ?? '#7367f0';
                } else {
                    // Fall through to next priority
                    $workshopRegistration = null;
                }
            }

            // If no workshop designation found, check member type name tag
            if (! isset($designation) && $conferenceMemberTypeNameTag) {
                $designation = $conferenceMemberTypeNameTag->name_tag;
                $color = $conferenceMemberTypeNameTag->color;

                if ((int) ($passSetting?->include_country_for_international ?? 0) === 1 &&
                    $participant->isInternationalParticipant()) {
                    $designationCountryName = $participant->user?->userDetail?->country?->country_name;
                }
            } elseif (! isset($designation)) {
                // Fallback: Try to get any name tag for this registrant type (ignore member_type)
                $fallbackNameTag = ConferenceMemberTypeNameTag::where('conference_id', $conference->id)
                    ->where('registrant_type', $participant->registrant_type)
                    ->first();

                if ($fallbackNameTag) {
                    $designation = $fallbackNameTag->name_tag;
                    $color = $fallbackNameTag->color ?? '#7367f0';

                    if ((int) ($passSetting?->include_country_for_international ?? 0) === 1 &&
                        $participant->isInternationalParticipant()) {
                        $designationCountryName = $participant->user?->userDetail?->country?->country_name;
                    }
                } else {
                    // Ultimate fallback based on registrant type
                    $registrantTypes = [
                        1 => 'Attendee',
                        2 => 'Speaker/Presenter',
                        3 => 'Session Chair',
                        4 => 'Special Guest',
                        5 => 'Organizer',
                    ];
                    $designation = $registrantTypes[$participant->registrant_type] ?? 'Participant';
                    $color = '#7367f0';
                }
            }
        }
        if (! $passSetting) {
            return redirect()->back()->with('delete', 'Please Create Pass Setting');
        }

        return view('backend.conference.conference-registration.individual-pass', compact('participant', 'passSetting', 'designation', 'conference', 'color', 'designationCountryName'));
    }

    public function generateCertificate($society, $conference, ConferenceRegistration $conferenceRegistration)
    {
        // Load relationships
        $conference->load('conferenceCertificate', 'ConferenceVenueDetail', 'conferenceSetting');
        $conferenceRegistration->load('user.userDetail.namePrefix');
        
        // Get registrant name with prefix
        $user = $conferenceRegistration->user;
        $registrantName = '';
        if ($user && $user->userDetail) {
            $prefix = $user->userDetail->namePrefix->prefix ?? '';
            $registrantName = trim($prefix . ' ' . $user->fullName($user));
        }
        
        // Get registrant type text
        $registrantType = '';
        switch ($conferenceRegistration->registrant_type) {
            case ConferenceRegistration::REGISTRANT_ATTENDEE:
                $registrantType = 'Delegate';
                break;
            case ConferenceRegistration::REGISTRANT_SPEAKER:
                $registrantType = 'Speaker';
                break;
            case ConferenceRegistration::REGISTRANT_SESSION_CHAIR:
                $registrantType = 'Session Chair';
                break;
            case ConferenceRegistration::REGISTRANT_SPECIAL_GUEST:
                $registrantType = 'Special Guest';
                break;
            case ConferenceRegistration::REGISTRANT_ORGANIZER:
                $registrantType = 'Organizer';
                break;
            case ConferenceRegistration::REGISTRANT_FACULTY:
                $registrantType = 'Faculty';
                break;
            case ConferenceRegistration::REGISTRANT_VOLUNTEER:
                $registrantType = 'Volunteer';
                break;
            case ConferenceRegistration::REGISTRANT_INVITEE:
                $registrantType = 'Invitee';
                break;
        }

        // Build presentation type label (e.g. "Oral-Original", "Poster-Review") when setting is enabled and registrant is a Speaker
        $presentationTypeLabel = null;
        if (
            $conferenceRegistration->registrant_type === ConferenceRegistration::REGISTRANT_SPEAKER &&
            $conference->conferenceCertificate && ($conference->conferenceCertificate->show_presentation_type ?? false)
        ) {
            $submission = Submission::where('user_id', $conferenceRegistration->user_id)
                ->where('conference_id', $conference->id)
                ->where('status', 1)
                ->where('request_status', 1)
                ->with('articleType')
                ->first();

            if ($submission) {
                $articleTypeName = $submission->articleType->name ?? null;
                if ($submission->presentation_type == 2) {
                    $presentationTypeLabel = 'Oral-Original' ;
                } elseif ($submission->presentation_type == 1) {
                    $presentationTypeLabel = 'Poster-Review';
                }
            }
        }

        return view('backend.conference.conference-registration.generate-certificate', compact(
            'conference',
            'conferenceRegistration',
            'registrantName',
            'registrantType',
            'presentationTypeLabel'
        ));
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
        // conference amount
        $conferenceAmount = '';
        if (! empty($conference)) {
            $createdAt = strtotime($conferenceRegistration->created_at);
            $today = strtotime(date('Y-m-d'));

            $earlyBirdDeadline = strtotime($conference->early_bird_registration_deadline);
            $regularDeadline = strtotime($conference->regular_registration_deadline);

            if ($earlyBirdDeadline >= $today && $earlyBirdDeadline >= $createdAt) {
                $conferenceAmount = ! empty($memberTypePrice->early_bird_amount) ? $memberTypePrice->early_bird_amount : '';
            } elseif ($regularDeadline >= $today && $regularDeadline >= $createdAt) {
                $conferenceAmount = ! empty($memberTypePrice->regular_amount) ? $memberTypePrice->regular_amount : '';
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
                'amount' => $workshop->amount,
            ];
        }

        $serviceCharge = $user->userDetail->country_id != 125 ? $conferenceRegistration->amount * 0.035 : null;
        $accompanyData = null;
        if ($conferenceRegistration->total_attendee > 1) {
            $accompanyData = [
                'accompany_person' => $conferenceRegistration->total_attendee - 1,
                'amount' => $memberTypePrice->guest_amount,
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
            'transactionId' => $conferenceRegistration->transaction_id,
            'amount' => $conferenceRegistration->amount,
            'amountInWord' => numberToWord($conferenceRegistration->amount),
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
            'conferenceAmount' => $conferenceAmount,
            'addons' => $addonsData,
            'workshop' => $workshopData,
            'accompany' => $accompanyData,
            'serviceCharge' => $serviceCharge,
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
        if (! empty($checkMeal)) {
            $totalLunchRemaining =
                $participant->total_attendee - $checkMeal->lunch_taken;
            $totalDinnerRemaining =
                $participant->total_attendee - $checkMeal->dinner_taken;
        }
        $passSetting = PassSetting::where('conference_id', $participant->conference_id)->first();
        // dd($passSetting);
        // Load halls with scientific sessions and polls
        $halls = Hall::where('conference_id', $participant->conference_id)
            ->with(['scientificSessions' => function ($query) {
                $query->whereDate('day', date('Y-m-d'))
                    ->with(['category', 'hall', 'sessionChair', 'submission'])
                    ->orderBy('scientific_session_category_id')
                    ->orderBy('start_time');
            }])
            ->get();

        // Group sessions by category for each hall
        foreach ($halls as $hall) {
            $hall->sessionsByCategory = $hall->scientificSessions->groupBy('scientific_session_category_id');
        }
        // dd($halls);
        // Load all polls for today's scientific sessions with answers and user votes
        $polls = Poll::whereHas('scientificSession', function ($query) use ($participant) {
            $query->where('conference_id', $participant->conference_id)->whereDate('day', date('Y-m-d'));
        })
            ->with(['answers.votes', 'scientificSession'])
            ->get()
            ->map(function ($poll) use ($participant) {
                $poll->user_voted = UserVote::where('conference_registration_id', $participant->id)
                    ->where('poll_id', $poll->id)
                    ->exists();
                $poll->user_answer_id = UserVote::where('conference_registration_id', $participant->id)
                    ->where('poll_id', $poll->id)
                    ->value('poll_answer_id');

                return $poll;
            });

        return view('backend.conference.conference-registration.attendance-profile', compact('participant', 'checkAttendance', 'totalLunchRemaining', 'totalDinnerRemaining', 'conferenceRegistrationKit', 'passSetting', 'halls', 'polls'));
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

            if (! $participant) {
                return response()->json(['success' => false, 'message' => 'Participant not found.'], 404);
            }

            $passSetting = PassSetting::where('conference_id', $participant->conference_id)->first();

            if (! $passSetting) {
                return response()->json(['success' => false, 'message' => 'Meal settings not found.'], 404);
            }

            $isLunch = ($currentTime >= $passSetting->lunch_start_time && $currentTime <= $passSetting->lunch_end_time);
            $isDinner = ($currentTime >= $passSetting->dinner_start_time && $currentTime <= $passSetting->dinner_end_time);

            if (! $isLunch && ! $isDinner) {
                return response()->json(['success' => false, 'message' => 'Meal is not available at this time.'], 403);
            }

            $mealRecord = Meal::where('conference_registration_id', $request->participant_id)
                ->whereDate('created_at', $today)
                ->first();

            if (! $mealRecord) {
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
                'remaining' => $remaining,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function vote(Request $request)
    {
        try {
            $participant = ConferenceRegistration::find($request->participant_id);

            if (! $participant) {
                return response()->json(['success' => false, 'message' => 'Participant not found.'], 404);
            }

            // Check if user has attendance for today
            $checkAttendance = $participant
                ->attendances()
                ->where(['conference_registration_id' => $participant->id, 'status' => 1])
                ->whereDate('created_at', date('Y-m-d'))
                ->first();

            if (! $checkAttendance) {
                return response()->json(['success' => false, 'message' => 'You must mark attendance before voting.'], 403);
            }

            // Check if already voted for this poll
            $existingVote = UserVote::where('conference_registration_id', $participant->id)
                ->where('poll_id', $request->poll_id)
                ->first();

            if ($existingVote) {
                return response()->json(['success' => false, 'message' => 'You have already voted for this poll.'], 403);
            }

            // Create vote
            UserVote::create([
                'conference_registration_id' => $participant->id,
                'poll_id' => $request->poll_id,
                'poll_answer_id' => $request->answer_id,
            ]);

            // Calculate and return results
            $poll = Poll::with('answers.votes')->find($request->poll_id);
            $results = $this->calculatePollResults($poll);

            return response()->json([
                'success' => true,
                'results' => $results,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function calculatePollResults($poll)
    {
        $totalVotes = UserVote::where('poll_id', $poll->id)->count();
        $results = [];

        foreach ($poll->answers as $answer) {
            $voteCount = $answer->votes->count();
            $percentage = $totalVotes > 0 ? round(($voteCount / $totalVotes) * 100, 1) : 0;

            $results[] = [
                'id' => $answer->id,
                'text' => $answer->answer_text,
                'votes' => $voteCount,
                'percentage' => $percentage,
            ];
        }

        return $results;
    }

    public function generateDummyPass(Request $request, $society, $conference)
    {
        $request->validate([
            'dummy_count' => 'required|integer|min:1|max:100',
            'registrant_type' => 'required|integer|in:1,2,3,4,5',
            'country_scope' => 'required|in:national,international',
        ]);

        $dummyCount = $request->dummy_count;
        $registrantType = $request->registrant_type;
        $countryScope = $request->country_scope;
        $dummyTransactionPrefix = $countryScope === 'international' ? 'INT-DUMMY-' : 'NAT-DUMMY-';

        // Create and save dummy registrant objects to database
        $savedRegistrants = collect();

        for ($i = 1; $i <= $dummyCount; $i++) {
            // Create and save dummy registration to database
            $dummyRegistrant = ConferenceRegistration::create([
                'conference_id' => $conference->id,
                'registrant_type' => $registrantType,
                'user_id' => null, // No user linked
                'token' => \Str::random(32),
                'status' => 1,
                'verified_status' => 1, // Auto-verify dummy passes
                'payment_type' => null,
                'transaction_id' => $dummyTransactionPrefix.\Str::upper(\Str::random(10)),
                'amount' => 0,
                'total_attendee' => 1,
            ]);

            // Reload with conference relationship
            $dummyRegistrant->load('conference');

            // Create a mock user object with userDetail
            $dummyUserModel = new class
            {
                public $userDetail;

                public $societies;

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

                    $this->societies = collect();
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

        $passSetting = PassSetting::where(['conference_id' => $conference->id, 'status' => 1])->first();

        if (! $passSetting) {
            return redirect()->back()->with('delete', 'Please Create Pass Setting');
        }

        // Process each registrant with designation
        $registrantsWithDesignation = $savedRegistrants->map(function ($participant) use ($conference) {
            // For dummy passes, we'll use ConferenceMemberTypeNameTag as fallback
            // Get any name tag configuration for this registrant type (regardless of member_type)
            $conferenceMemberTypeNameTag = ConferenceMemberTypeNameTag::where('conference_id', $conference->id)
                ->where('registrant_type', $participant->registrant_type)
                ->first();

            // Fallback to committee designation if no member type name tag found
            if (! $conferenceMemberTypeNameTag) {
                $conferenceCommitteePassDesignation = ConferenceCommitteePassDesignation::where('conference_id', $conference->id)
                    ->whereHas('designation')
                    ->first();

                if ($conferenceCommitteePassDesignation) {
                    $designation = $conferenceCommitteePassDesignation->name_tag;
                    $color = $conferenceCommitteePassDesignation->color ?? '#7367f0';
                } else {
                    // Ultimate fallback based on registrant type
                    $registrantTypes = [
                        1 => 'Attendee',
                        2 => 'Speaker/Presenter',
                        3 => 'Session Chair',
                        4 => 'Special Guest',
                        5 => 'Organizer',
                    ];
                    $designation = $registrantTypes[$participant->registrant_type] ?? 'Participant';
                    $color = '#7367f0';
                }
            } else {
                $designation = $conferenceMemberTypeNameTag->name_tag;
                $color = $conferenceMemberTypeNameTag->color ?? '#7367f0';
            }

            $participant->designation = $designation;
            $participant->designation_color = $color;

            return $participant;
        });

        return view('backend.conference.conference-registration.bulk-pass', [
            'registrants' => $registrantsWithDesignation,
            'passSetting' => $passSetting,
            'conference' => $conference,
        ]);
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

    /**
     * Update registration IDs for all registrants
     */
    public function updateRegistrationIds(Request $request, $society, $conference)
    {
        try {
            // Increase limits for large datasets
            ini_set('memory_limit', '1024M');
            ini_set('max_execution_time', '600'); 
            set_time_limit(600); 

            $stats = ConferenceRegistration::updateRegistrationIds($conference->id);
                // dd($stats);
            return redirect() 
                ->back()
                ->with('status', "Registration IDs updated successfully! Total: {$stats['total']} 
                    (Invited: {$stats['invited']}, Participants: {$stats['participant']}, 
                    Speakers: {$stats['speaker']}, Session Chairs: {$stats['session_chair']}, 
                    Special Guests: {$stats['special_guest']}, Organizers: {$stats['organizer']})");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('delete', 'Error updating registration IDs: '.$e->getMessage());
        }
    }

    /**
     * Update registration IDs for selected registrant type only
     */
    public function updateRegistrationIdsByType(Request $request, $society, $conference)
    {
        try {
            $validated = $request->validate([
                'registrant_type' => 'required|integer|in:1,2,3,4,5,6,7',
            ]);

            $stats = ConferenceRegistration::updateRegistrationIdsByRegistrantType(
                $conference->id,
                (int) $validated['registrant_type']
            );

            return redirect()
                ->back()
                ->with('status', "Registration IDs updated successfully for {$stats['label']}! Total updated: {$stats['total']}");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('delete', 'Error updating registration IDs for selected type: '.$e->getMessage());
        }
    }

    /**
     * Update registration IDs for selected country scope only.
     */
    public function updateRegistrationIdsByCountryScope(Request $request, $society, $conference)
    {
        try {
            $validated = $request->validate([
                'country_scope' => 'required|in:national,international',
            ]);

            $stats = ConferenceRegistration::updateRegistrationIdsByCountryScope(
                $conference->id,
                $validated['country_scope']
            );

            return redirect()
                ->back()
                ->with('status', "Registration IDs updated successfully for {$stats['label']} scope! Total updated: {$stats['total']} (Real: {$stats['real']}, Dummy: {$stats['dummy']})");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('delete', 'Error updating registration IDs for selected country scope: '.$e->getMessage());
        }
    }

    /**
     * Bulk update registrant type with advanced filtering
     */
    public function bulkUpdateRegistrantType(Request $request, $society, $conference)
    {
        try {
            // Increase limits for large datasets
            ini_set('memory_limit', '1024M');
            ini_set('max_execution_time', '600');
            set_time_limit(600);

            $selectedIds = [];
            if ($request->filled('selected_registrant_ids')) {
                $selectedIds = array_filter(array_map('intval', explode(',', $request->selected_registrant_ids)));
            }

            // Validate input
            $validated = $request->validate([
                'from_type' => 'nullable|integer|in:1,2,3,4,5,6,7,8',
                'to_type' => 'required|integer|in:1,2,3,4,5,6,7,8',
                'selected_registrant_ids' => 'nullable|string',
                'filter_is_invited' => 'nullable|in:0,1',
                'filter_verified_status' => 'nullable|in:0,1,2',
                'filter_country_scope' => 'nullable|in:national,international',
                'filter_certificate_required' => 'nullable|in:0,1',
                'filter_attend_type' => 'nullable|in:1,2',
            ]);

            if (!empty($selectedIds)) {
                // Use selected registrations mode
                $stats = ConferenceRegistration::bulkUpdateRegistrantTypeByIds(
                    $selectedIds,
                    $conference->id,
                    (int) $validated['to_type']
                );

                $toTypeLabel = ConferenceRegistration::getRegistrantTypeLabel((int) $validated['to_type']);

                return redirect()
                    ->back()
                    ->with('status', "Successfully converted {$stats['updated']} registrations to {$toTypeLabel}");
            }

            if (empty($validated['from_type'])) {
                return redirect()
                    ->back()
                    ->with('delete', 'Please select a source registrant type when no registrations are selected.');
            }

            // Reject if FROM and TO are the same for the fallback bulk path
            if ((int) $validated['from_type'] === (int) $validated['to_type']) {
                return redirect()
                    ->back()
                    ->with('delete', 'Error: Source and target registrant types cannot be the same.');
            }

            // Build filters array for filter-based conversion
            $filters = array_filter([
                'is_invited' => ($validated['filter_is_invited'] ?? null) !== null ? (bool) $validated['filter_is_invited'] : null,
                'verified_status' => ($validated['filter_verified_status'] ?? null) !== null ? (int) $validated['filter_verified_status'] : null,
                'country_scope' => $validated['filter_country_scope'] ?? null,
                'certificate_required' => ($validated['filter_certificate_required'] ?? null) !== null ? (bool) $validated['filter_certificate_required'] : null,
                'attend_type' => ($validated['filter_attend_type'] ?? null) !== null ? (int) $validated['filter_attend_type'] : null,
            ], fn($value) => $value !== null);

            // Call the bulk update method with filters
            $filters['from_type'] = (int) $validated['from_type'];
            $stats = ConferenceRegistration::bulkUpdateRegistrantTypeWithFilters(
                $conference->id,
                (int) $validated['to_type'],
                $filters
            );

            // Build success message
            $fromTypeLabel = ConferenceRegistration::getRegistrantTypeLabel((int) $validated['from_type']);
            $toTypeLabel = ConferenceRegistration::getRegistrantTypeLabel((int) $validated['to_type']);
            $filtersSummary = '';

            if (!empty($filters)) {
                $filtersSummary = ' with filters: ';
                $appliedFilters = [];
                if (isset($filters['is_invited'])) {
                    $appliedFilters[] = $filters['is_invited'] ? 'Invited' : 'Self-Registered';
                }
                if (isset($filters['verified_status'])) {
                    $statusMap = [0 => 'Unverified', 1 => 'Verified', 2 => 'Rejected'];
                    $appliedFilters[] = $statusMap[$filters['verified_status']];
                }
                if (isset($filters['country_scope'])) {
                    $appliedFilters[] = ucfirst($filters['country_scope']);
                }
                if (isset($filters['certificate_required'])) {
                    $appliedFilters[] = $filters['certificate_required'] ? 'Certificate Required' : 'No Certificate';
                }
                if (isset($filters['attend_type'])) {
                    $appliedFilters[] = $filters['attend_type'] == 1 ? 'Physical' : 'Online';
                }
                $filtersSummary .= implode(', ', $appliedFilters);
            }

            return redirect()
                ->back()
                ->with('status', "Successfully converted {$stats['updated']} registrations from {$fromTypeLabel} to {$toTypeLabel}{$filtersSummary}");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('delete', 'Error during bulk registrant type conversion: '.$e->getMessage());
        }
    }

    private function applyCountryScopeFilter($query, Request $request): void
    {
        if (! $request->filled('country_scope')) {
            return;
        }

        if ($request->country_scope === 'national') {
            $query->where(function ($q) {
                $q->whereHas('user.userDetail', function ($subQuery) {
                    $subQuery->where('country_id', 125);
                })
                    ->orWhere(function ($dummyQuery) {
                        $dummyQuery->whereNull('user_id')
                            ->where('transaction_id', 'like', 'NAT-DUMMY-%');
                    });
            });

            return;
        }

        if ($request->country_scope === 'international') {
            $query->where(function ($q) {
                $q->whereHas('user.userDetail', function ($subQuery) {
                    $subQuery->where('country_id', '!=', 125);
                })
                    ->orWhere(function ($dummyQuery) {
                        $dummyQuery->whereNull('user_id')
                            ->where('transaction_id', 'like', 'INT-DUMMY-%');
                    });
            });
        }
    }

    /**
     * Show bulk email form
     */
    public function showBulkEmailForm(Request $request, $society, $conference)
    {
        $name_prefiexs = NamePrefix::where('status', 1)->get();
        $countries = \App\Models\User\Country::where('status', 1)->orderBy('country_name')->get();

        return view('backend.conference.conference-registration.bulk-email', compact(
            'society',
            'conference',
            'name_prefiexs',
            'countries'
        ));
    }

    /**
     * Get bulk email users (for autocomplete/tagify)
     */
    public function getBulkEmailUsers(Request $request, $society, $conference)
    {
        try {
            $society_id = $society->id;
            $query = ConferenceRegistration::with([
                'user' => function ($query) use ($society_id) {
                    $query->with([
                        'userDetail.namePrefix',
                        'userDetail.country',
                        'societies' => function ($q) use ($society_id) {
                            $q->where('society_id', $society_id)
                                ->withPivot('member_type_id');
                        },
                    ]);
                },
                'conference',
            ])
                ->where('conference_id', $conference->id)
                ->where('status', 1)
                ->whereNotNull('user_id'); // Only get real users

            // Apply filters
            if ($request->filled('registrant_type')) {
                $query->where('registrant_type', $request->registrant_type);
            }

            if ($request->filled('is_invited')) {
                $query->where('is_invited', $request->is_invited);
            }

            if ($request->filled('verified_status')) {
                $query->where('verified_status', $request->verified_status);
            }

            if ($request->filled('payment_type')) {
                $query->where('payment_type', $request->payment_type);
            }

            if ($request->filled('from')) {
                $query->whereDate('created_at', '>=', $request->from);
            }

            if ($request->filled('to')) {
                $query->whereDate('created_at', '<=', $request->to);
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

            // Filter by attendance status
            if ($request->filled('attendance_status')) {
                if ($request->attendance_status == '1') {
                    // Has attended - has attendance record with status 1
                    $query->whereHas('attendances', function ($q) {
                        $q->where('status', 1);
                    });
                } elseif ($request->attendance_status == '0') {
                    // Not attended - no attendance record or status != 1
                    $query->whereDoesntHave('attendances', function ($q) {
                        $q->where('status', 1);
                    });
                }
            }

            $registrants = $query->get();

            // Transform to Tagify format
            $users = $registrants->map(function ($registrant) {
                $user = $registrant->user;
                if (!$user) {
                    return null;
                }

                $middleName = !empty($user->m_name) ? ' ' . $user->m_name : '';
                $fullName = trim($user->f_name . $middleName . ' ' . $user->l_name);

                return [
                    'id' => $user->id,
                    'name' => $fullName,
                    'email' => $user->email,
                    'avatar' => $user->profile_image ? asset('storage/' . $user->profile_image) : asset('default-image/user-profile.png'),
                    'title' => $fullName . ' (' . $user->email . ')',
                    'class' => 'user-tag',
                ];
            })->filter()->values();

            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    /**
     * Send bulk email to registrants
     */
    public function sendBulkEmail(Request $request, $society, $conference)
    {
        try {
            $request->validate([
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            // Increase limits for large datasets
            ini_set('memory_limit', '1024M');
            ini_set('max_execution_time', '600');
            set_time_limit(600);

            $society_id = $society->id;
            $query = ConferenceRegistration::with([
                'user' => function ($query) use ($society_id) {
                    $query->with([
                        'userDetail.namePrefix',
                        'userDetail.country',
                        'societies' => function ($q) use ($society_id) {
                            $q->where('society_id', $society_id)
                                ->withPivot('member_type_id');
                        },
                    ]);
                },
                'conference',
            ])
                ->where('conference_id', $conference->id)
                ->where('status', 1)
                ->whereNotNull('user_id'); // Only send to real users

            // If selectedUserIds is provided, use those specific users
            if ($request->filled('selectedUserIds')) {
                $userIds = array_filter(array_map('intval', explode(',', $request->selectedUserIds)));
                if (! empty($userIds)) {
                    $query->whereIn('user_id', $userIds);
                } else {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('delete', 'No valid user IDs selected.');
                }
            } else {
                // Apply filters (only if selectedUserIds not provided)
                if ($request->filled('registrant_type')) {
                    $query->where('registrant_type', $request->registrant_type);
                }

                if ($request->filled('is_invited')) {
                    $query->where('is_invited', $request->is_invited);
                }

                if ($request->filled('verified_status')) {
                    $query->where('verified_status', $request->verified_status);
                }

                if ($request->filled('payment_type')) {
                    $query->where('payment_type', $request->payment_type);
                }

                if ($request->filled('from')) {
                    $query->whereDate('created_at', '>=', $request->from);
                }

                if ($request->filled('to')) {
                    $query->whereDate('created_at', '<=', $request->to);
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

                // Filter by attendance status
                if ($request->filled('attendance_status')) {
                    if ($request->attendance_status == '1') {
                        // Has attended - has attendance record with status 1
                        $query->whereHas('attendances', function ($q) {
                            $q->where('status', 1);
                        });
                    } elseif ($request->attendance_status == '0') {
                        // Not attended - no attendance record or status != 1
                        $query->whereDoesntHave('attendances', function ($q) {
                            $q->where('status', 1);
                        });
                    }
                }
            }

            $registrants = $query->get();
            if ($registrants->isEmpty()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('delete', 'No registrants found matching the selected criteria.');
            }

            $queuedCount = 0;
            $skippedCount = 0;

            foreach ($registrants as $registrant) {
                if (! $registrant->user || ! $registrant->user->email) {
                    $skippedCount++;
                    continue;
                }

                // Prepare data with placeholders
                $messageContent = $this->replacePlaceholders(
                    $request->message,
                    $registrant,
                    $conference,
                    $society
                );

                $data = [
                    'name' => $registrant->user->fullName($registrant->user),
                    'namePrefix' => $registrant->user->userDetail->namePrefix->prefix ?? '',
                    'registrant_type' => $registrant->registrant_type,
                    'registration_id' => $registrant->registration_id,
                    'conference_name' => $conference->conference_name,
                ];

                // Dispatch job with delay to prevent rate limiting (5 seconds per email)
                // Increases interval to 5 seconds to stay within Mailtrap rate limits
                SendRegistrantEmailJob::dispatch(
                    $registrant->id,
                    $request->subject,
                    $messageContent,
                    $data, 
                    $conference->conference_name
                )->delay(now()->addSeconds($queuedCount * 5));

                $queuedCount++;
            }

            $message = "Email queued successfully for {$queuedCount} recipient(s). Emails will be sent with 5-second intervals.";
            if ($skippedCount > 0) {
                $message .= " Skipped {$skippedCount} recipient(s) with invalid data.";
            }

            return redirect()
                ->back()
                ->with('status', $message);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('delete', 'Error sending emails: '.$e->getMessage());
        }
    }

    /**
     * Show individual email form
     */
    public function showIndividualEmailForm($society, $conference, ConferenceRegistration $registrant)
    {
        if (! $registrant->user) {
            return redirect()
                ->back()
                ->with('delete', 'Cannot send email to registrant without user account.');
        }

        return view('backend.conference.conference-registration.individual-email', compact(
            'society',
            'conference',
            'registrant'
        ));
    }

    /**
     * Send individual email to registrant
     */
    public function sendIndividualEmail(Request $request, $society, $conference, ConferenceRegistration $registrant)
    {
        try {
            $request->validate([
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            if (! $registrant->user || ! $registrant->user->email) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('delete', 'Cannot send email to registrant without valid email address.');
            }

            // Prepare data with placeholders
            $messageContent = $this->replacePlaceholders(
                $request->message,
                $registrant,
                $conference,
                $society
            );

            $data = [
                'name' => $registrant->user->fullName($registrant->user),
                'namePrefix' => $registrant->user->userDetail->namePrefix->prefix ?? '',
                'registrant_type' => $registrant->registrant_type,
                'registration_id' => $registrant->registration_id,
                'conference_name' => $conference->conference_name,
            ];

            // Dispatch job to queue
            SendRegistrantEmailJob::dispatch(
                $registrant->id,
                $request->subject,
                $messageContent,
                $data,
                $conference->conference_name
            );

            return redirect()
                ->route('conference.conference-registration.index', [$society, $conference])
                ->with('status', 'Email queued successfully for '.$registrant->user->email);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('delete', 'Error queueing email: '.$e->getMessage());
        }
    }

    /**
     * Replace placeholders in message with actual values
     */
    private function replacePlaceholders($message, $registrant, $conference, $society)
    {
        $registrantTypes = [
            1 => 'Attendee',
            2 => 'Speaker/Presenter',
            3 => 'Session Chair',
            4 => 'Special Guest',
            5 => 'Organizer',
            6 => 'Faculty',
            7 => 'Volunteer',
        ];

        $certificateUrl = route('conference.conference-registration.generateCertificate', [
            $society,
            $conference,
            $registrant->id
        ]);

        $placeholders = [
            '{name}' => $registrant->user->fullName($registrant->user),
            '{first_name}' => $registrant->user->f_name,
            '{last_name}' => $registrant->user->l_name,
            '{prefix}' => $registrant->user->userDetail->namePrefix->prefix ?? '',
            '{email}' => $registrant->user->email,
            '{registrant_type}' => $registrantTypes[$registrant->registrant_type] ?? 'N/A',
            '{registration_id}' => $registrant->registration_id ?? 'N/A',
            '{conference_name}' => $conference->conference_name,
            '{conference_theme}' => $conference->conference_theme,
            '{conference_start_date}' => $conference->start_date ? \Carbon\Carbon::parse($conference->start_date)->format('jS F, Y') : 'N/A',
            '{conference_end_date}' => $conference->end_date ? \Carbon\Carbon::parse($conference->end_date)->format('jS F, Y') : 'N/A',
            '{venue}' => $conference->ConferenceVenueDetail->venue_name ?? 'N/A',
            '{venue_address}' => $conference->ConferenceVenueDetail->venue_address ?? 'N/A',
            '{certificate_link}' => $certificateUrl,
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $message);
    }
}
