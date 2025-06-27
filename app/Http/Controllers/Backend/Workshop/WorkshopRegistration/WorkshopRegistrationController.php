<?php

namespace App\Http\Controllers\Backend\Workshop\WorkshopRegistration;

use App\Http\Controllers\Controller;
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
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class WorkshopRegistrationController extends Controller
{
    public function __construct(protected FileService $file_service) {}

    public function index($society, $conference, $workshop)
    {
        $registrations = WorkshopRegistration::where(['workshop_id' => $workshop->id, 'registrant_type' => 1, 'status' => 1])->get();
        return view('backend.workshop.workshop-registration.index', compact('registrations', 'workshop'));
    }

    public function registerForExceptionalCase($society, $conference)
    {
        // $conferenceDetail = conference_detail();
        // if (empty($conferenceDetail)) {
        //     return redirect()->route('dashboard');
        // }
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
                'payment_voucher' => 'nullable|mimes:jpg,png,pdf|max:250'
            ];

            $validated = $request->validate($rules);

            $checkUserRegistrationInWorkshop = WorkshopRegistration::where(['workshop_id' => $request->workshop_id, 'user_id' => $request->user_id, 'status' => 1])->first();

            if (empty($checkUserRegistrationInWorkshop)) {
                $validated['token'] = random_word(60);
                $validated['verified_status'] = 1;
                $validated['payment_type'] = 3;

                if (!empty($validated['payment_voucher'])) {

                    $validated['payment_voucher'] = $this->file_service->fileUpload($validated['payment_voucher'], 'payment_voucher', 'workshop/payment-voucher');
                }

                DB::beginTransaction();


                WorkshopRegistration::create($validated);

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
        // $conferenceDetail = conference_detail();
        // if (empty($conferenceDetail)) {
        //     return redirect()->route('dashboard');
        // }
        $workshops = Workshop::where(['conference_id' => $conference->id, 'status' => 1])->get();
        $prefixesAll = NamePrefix::whereStatus(1)->get();
        return view('backend.workshop.workshop-registration.register-for-new-user', compact('workshops', 'prefixesAll', 'conference', 'society'));
    }

    public function registerForNewUserSubmit(Request $request)
    {
        try {
            $checkUser = User::whereEmail($request->email)->first();

            $workshop = WorkshopRegistration::where(['workshop_id' => $request->workshop_id, 'user_id' => $checkUser->id])->first();
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
            $validated['payment_type'] = 3;
            $validated['type'] = 3;

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

            // insert table-3
            WorkshopRegistration::create($validated);
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
}
