<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conference\ConferenceRegistration;
use App\Models\User;
use App\Models\User\UserDetail;
use App\Services\File\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ConferenceRegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(protected FileService $file_service) {}

    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'f_name' => 'required|string|max:255',
                'm_name' => 'nullable|string|max:255',
                'l_name' => 'required|string|max:255',
                'country_id' => 'required|integer|exists:countries,id',
                'member_type_id' => 'required|integer|exists:member_types,id',
                'email' => 'required|email|max:255|unique:users,email',
                'name_prefix_id' => 'required|integer|exists:name_prefixes,id',
                'gender' => 'required|integer|in:1,2,3',
                'phone' => 'required|string|max:20',
                'registrant_type' => 'required|integer',
                'payment_type' => 'required|integer',
                'payment_voucher' => 'nullable|string|max:255',
                'amount' => 'required|string|max:255',
                'transaction_id' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            $paymentVoucherPath = null;
            if ($request->hasFile('payment_voucher')) {
                $paymentVoucherPath = $this->file_service->fileUpload(
                    $request->file('payment_voucher'),
                    'payment_voucher',
                    'conference/payment-voucher'
                );
            }
            $password = random_word(5);
            DB::beginTransaction();

            $user = User::create([
                'f_name' => $request->f_name,
                'm_name' => $request->m_name,
                'l_name' => $request->l_name,
                'email' => $request->email,
                'type' => 3,
                'password' => hash_password($password)
            ]);

            UserDetail::create([
                'user_id' => $user->id,
                'country_id' => $request->country_id,
                'name_prefix_id' => $request->name_prefix_id,
                'gender' => $request->gender,
                'phone' => $request->phone
            ]);

            $user->societies()->attach(1, [
                'member_type_id' => $request->member_type_id,
            ]);


            $registration = ConferenceRegistration::create([
                'user_id' => $user->id,
                'conference_id' => 1,
                'registrant_type' => $request->registrant_type,
                'attend_type' => 1,
                // 'payment_type' => $request->payment_type,
                'payment_voucher' => $paymentVoucherPath,
                'amount' => $request->amount,
                'transaction_id' => $request->transaction_id,
                'verified_status' => 1,
                'total_attendee' => 1,
                'token' => random_word(60),
            ]);
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Conference registration registered successfully',
                'data' => $registration
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Internel Server Error',
                'data' => []
            ], 400);
            //throw $th;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
