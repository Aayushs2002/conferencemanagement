<?php

namespace App\Http\Controllers\Backend\Workshop\WorkshopRegistration;

use App\Http\Controllers\Controller;
use App\Mail\Workshop\Registration\AcceptMail;
use App\Mail\Workshop\Registration\RegistrationByAdminMail;
use App\Mail\Workshop\Registration\RejectMail;
use App\Models\Conference\ConferenceSetting;
use App\Models\Conference\PassSetting;
use App\Models\User;
use App\Models\User\Institution;
use App\Models\User\NamePrefix;
use App\Models\User\Society;
use App\Models\User\UserDetail;
use App\Models\User\UserSociety;
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
        $query = WorkshopRegistration::where(['workshop_id' => $workshop->id, 'registrant_type' => 1, 'status' => 1]);
        if ($request->filled('meal_type')) {
            $query->where('meal_type', $request->meal_type);
        }
        $registrations = $query->latest()->get();

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
                'name' => $workshopRegistration->user->userDetail->namePrefix->prefix . ' ' . $workshopRegistration->user->fullName($workshopRegistration->user),
                'workshopTitle' => $workshopRegistration->workshop->workshop_title,
                'conference_name' => $conference->conference_name
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
        $workshops = Workshop::where(['conference_id' => $conference->id, 'status' => 1])->get();
        $society = Society::with(['users' => function ($query) {
            $query->where('type', 3)->orderByDesc('id');
        }])->where([
            'id' => $conference->society_id,
            'status' => 1
        ])->first();

        $users = $society ? $society->users : collect();
        return view('backend.workshop.workshop-registration.register-for-exceptional-case', compact('workshops', 'users', 'society', 'conference'));
    }

    public function  registerForExceptionalCaseSubmit($society, $conference, Request $request)
    {
        try {
            $rules = [
                'workshop_id' => 'required',
                'user_id' => 'required',
                'transaction_id' => 'required|unique:workshop_registrations,transaction_id',
                'amount' => 'required|integer',
                'meal_type' => 'required',
                'payment_voucher' => 'nullable|mimes:jpg,png,pdf|max:250'
            ];

            $validated = $request->validate($rules);

            $checkUserRegistrationInWorkshop = WorkshopRegistration::where(['workshop_id' => $request->workshop_id, 'user_id' => $request->user_id, 'status' => 1])->first();

            if (empty($checkUserRegistrationInWorkshop)) {
                $validated['token'] = random_word(60);
                $validated['verified_status'] = 1;
                $validated['payment_type'] = 6;
                $date = \Carbon\Carbon::now()->format('F j, Y');

                if (!empty($validated['payment_voucher'])) {

                    $validated['payment_voucher'] = $this->file_service->fileUpload($validated['payment_voucher'], 'payment_voucher', 'workshop/payment-voucher');
                }
                $user = User::whereId($request->user_id)->first();
                $workshop = Workshop::whereId($validated['workshop_id'])->first();
                $conferenceSetting = ConferenceSetting::where('conference_id', $conference->id)->first();

                $workshopData = [];
                $workshopData[] = [
                    'name'   => $workshop->workshop_title ?? 'Workshop',
                    'amount' => $validated['amount']
                ];
                $mailData = [
                    'conference_theme' => $conference->conference_theme,
                    'conference_name'  => $conference->conference_name,
                    'name'             => $user->fullName($user),
                    'namePrefix'       => $user->userDetail->namePrefix->prefix,
                    'email'            => $user->email,
                    'paymentType'      => 'Online Payment',
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
                    'country'          => $user->userDetail->country_id,
                    'signatureName'    => $conferenceSetting->name,
                    'signature'        => $conferenceSetting->signature,
                    'conferenceAmount' => null,
                    'addons'           => [],
                    'workshop'         => $workshopData,
                    'accompany' => null,
                    'type' => 2,
                ];

                DB::beginTransaction();

                Mail::to($user->email)->send(new RegistrationByAdminMail($mailData, $conference->conference_name));

                WorkshopRegistration::create($validated);
                logActivity($conference->id, 'Registered Workshop', $user->fullName($user) . ' is registered to workshop');

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
        $workshops = Workshop::where(['conference_id' => $conference->id, 'status' => 1])->get();
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
                'certificate_required' => 'nullable'
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

            if (!empty($validated['payment_voucher'])) {
                $validated['payment_voucher'] = $this->file_service->fileUpload($validated['payment_voucher'], 'payment_voucher', 'workshop/payment-voucher');
            }
            // for values end

            DB::beginTransaction();
            // insert table-1
            $storeUser = User::create($validated);

            $validated['user_id'] = $storeUser->id;

            UserDetail::create($validated);

            //insert table-4
            $societyId = current_user()->societies->value('id');
            //insert table-3
            $storeUser->societies()->attach($societyId, [
                'member_type_id' => $validated['member_type_id'],
            ]);

            $workshop = Workshop::whereId($validated['workshop_id'])->first();
            $conferenceSetting = ConferenceSetting::where('conference_id', $conference->id)->first();

            $workshopData = [];
            $workshopData[] = [
                'name'   => $workshop->workshop_title ?? 'Workshop',
                'amount' => $validated['amount']
            ];
            $namePrefix = DB::table('name_prefixes')->whereId($validated['name_prefix_id'])->first()->prefix;
            $mailData = [
                'conference_theme' => $conference->conference_theme,
                'conference_name'  => $conference->conference_name,
                'name' => $request->f_name . ' ' . $request->m_name . ' ' . $request->l_name,
                'namePrefix'       => $namePrefix,
                'email'            => $request->email,
                'paymentType'      => 'Online Payment',
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
                'country'          => $request->country_id,
                'signatureName'    => $conferenceSetting->name,
                'signature'        => $conferenceSetting->signature,
                'conferenceAmount' => null,
                'addons'           => [],
                'workshop'         => $workshopData,
                'accompany' => null,
                'type' => 1,
            ];
            Mail::to($request->email)->send(new RegistrationByAdminMail($mailData, $conference->conference_name));

            // insert table-3
            WorkshopRegistration::create($validated);
            logActivity($conference->id, 'Registered Workshop', $request->f_name . ' ' . $request->m_name . ' ' . $request->l_name . ' is registered to workshop');

            DB::commit();

            return redirect()->back()->with('status', 'Successfully registered.');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function generatePass($workshop)
    {
        $registrant_type = request('registrant_type');
        $registrants = WorkshopRegistration::where(['workshop_id' => $workshop->id, 'registrant_type' => $registrant_type, 'status' => 1])->get();
        $passSetting = WorkshopPassSetting::where(['conference_id' => $workshop->conference_id, 'status' => 1])->first();
        return view('backend.workshop.pass.registrant-pass', compact('registrants', 'passSetting'));
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
                'amount' => $workshopRegistration->amount
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
            'transactionId'   => $workshopRegistration->transaction_id,
            'amount'          => $workshopRegistration->amount,
            'amountInWord'    => numberToWord($workshopRegistration->amount),
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
            'conferenceAmount' => null,
            'addons'           => [],
            'workshop'         => $workshopData,
            'accompany' => null,
            'serviceCharge' => null
        ];

        $pdf = Pdf::loadView('emails.conference.payment-voucher', ['data' => $data])
            ->setPaper('legal', 'portrait');

        return $pdf->download('payment-voucher.pdf');
    }
}
