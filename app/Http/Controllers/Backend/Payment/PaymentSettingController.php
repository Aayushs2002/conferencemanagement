<?php

namespace App\Http\Controllers\Backend\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment\InternationalPayment;
use App\Models\Payment\NationalPayment;
use Exception;
use Illuminate\Http\Request;

class PaymentSettingController extends Controller
{
    public function index($society)
    {
        // if (!empty(session()->get('conferenceDetail'))) {
        //     session()->forget('conferenceDetail');
        // }
        // $societyDetail = society_detail();

        // if (is_super_admin() && empty($societyDetail)) {
        //     return redirect()->route('dashboard');
        // }
        // if (is_society_admin()) {
        //     $nationalPayment = NationalPayment::where(['society_id' => current_user()->societies->value('id'), 'status' => 1])->first();
        //     $internationalPayment = InternationalPayment::where(['society_id' => current_user()->societies->value('id'), 'status' => 1])->first();
        // } elseif (is_super_admin()) {
        //     $nationalPayment = NationalPayment::where(['society_id' => $societyDetail->id, 'status' => 1])->first();
        //     $internationalPayment = InternationalPayment::where(['society_id' => $societyDetail->id, 'status' => 1])->first();
        // } else {
        //     return redirect()->route('dashboard');
        // } 
        $nationalPayment = NationalPayment::where(['society_id' => $society->id, 'status' => 1])->first();
        $internationalPayment = InternationalPayment::where(['society_id' => $society->id, 'status' => 1])->first();
        return view('backend.payment-setting.index', compact('nationalPayment', 'internationalPayment', 'society'));
    }

    public function store(Request $request, $society)
    {
        $section = $request->input('section');
        $activeTab = $request->input('active_tab');

        if ($section === 'national') {

            if ($activeTab === 'fonepay') {
                $validated = $request->validate([
                    'profile_id' => 'required|string|max:255',
                    'secret_key' => 'required|string|max:255',
                    'id' => 'nullable',
                ]);

                if (empty($validated['id'])) {
                    $validated['society_id'] = $society->id;
                    $validated['payment_type'] = 'fonepay';
                    $submitData = NationalPayment::create($validated);
                } else {
                    $nationalPayment = NationalPayment::whereId($validated['id'])->first();
                    $submitData = $nationalPayment->update($validated);
                }

                $message = empty($validated['id']) ? 'Successfully inserted FonePay payment.' : 'Successfully updated FonePay payment';
            } elseif ($activeTab === 'moco') {
                $validated = $request->validate([
                    'moco_merchant_id' => 'required|string|max:255',
                    'moco_outlet_id' => 'required|string|max:255',
                    'moco_terminal_id' => 'required|string|max:255',
                    'moco_shared_key' => 'required|string|max:255',
                    'id' => 'nullable',
                ]);

                if (empty($validated['id'])) {
                    $validated['society_id'] = $society->id;
                    $validated['payment_type'] = 'moco';
                    $submitData = NationalPayment::create($validated);
                } else {
                    $nationalPayment = NationalPayment::whereId($validated['id'])->first();
                    $submitData = $nationalPayment->update($validated);
                }

                $message = empty($validated['id']) ? 'Successfully inserted Moco payment.' : 'Successfully updated Moco payment';
            } elseif ($activeTab === 'esewa') {
                $validated = $request->validate([
                    'esewa_product_code' => 'required|string|max:255',
                    'esewa_secret_key' => 'required|string|max:255',
                    'id' => 'nullable',
                ]);

                if (empty($validated['id'])) {
                    $validated['society_id'] = $society->id;
                    $validated['payment_type'] = 'esewa';
                    $submitData = NationalPayment::create($validated);
                } else {
                    $nationalPayment = NationalPayment::whereId($validated['id'])->first();
                    $submitData = $nationalPayment->update($validated);
                }

                $message = empty($validated['id']) ? 'Successfully inserted Esewa payment.' : 'Successfully updated Esewa payment';
            } elseif ($activeTab === 'khalti') {
                $validated = $request->validate([
                    'khalti_live_secret_key' => 'required|string|max:255',
                    'id' => 'nullable',
                ]);

                if (empty($validated['id'])) {
                    $validated['society_id'] = $society->id;
                    $validated['payment_type'] = 'khalti';
                    $submitData = NationalPayment::create($validated);
                } else {
                    $nationalPayment = NationalPayment::whereId($validated['id'])->first();
                    $submitData = $nationalPayment->update($validated);
                }

                $message = empty($validated['id']) ? 'Successfully inserted Khalti payment.' : 'Successfully updated Khalti payment';
            }

            if (!$submitData) {
                throw new Exception("Error Processing Request", 1);
            }

            return response()->json(['message' => $message], 200);
        } elseif ($section === 'international') {
            // Handle different international payment tabs
            if ($activeTab === 'himalayan_bank') {
                $validated = $request->validate([
                    'merchant_key' => 'required|string|max:255',
                    'api_key' => 'required|string|max:255',
                    'access_token' => 'required',
                    'merchant_signing_private_key' => 'required',
                    'paco_encryption_public_key' => 'required',
                    'merchant_decryption_private_key' => 'required',
                    'paco_signing_public_key' => 'required',
                    'international_id' => 'nullable',
                ]);

                if (empty($validated['international_id'])) {
                    $validated['society_id'] = $society->id;
                    $validated['payment_type'] = 'himalayan_bank';
                    // dd($validated);
                    $submitData = InternationalPayment::create($validated);
                } else {
                    $internationalPayment = InternationalPayment::whereId($validated['international_id'])->first();
                    $submitData = $internationalPayment->update($validated);
                }

                $message = empty($validated['international_id']) ? 'Successfully inserted Himalayan Bank payment.' : 'Successfully updated Himalayan Bank payment';
            } else if ($activeTab === 'account_details') {
                $validated = $request->validate([
                    'bank_detail' => 'required',
                    'international_id' => 'nullable'
                ]);
                if (empty($validated['international_id'])) {
                    $validated['society_id'] = $society->id;
                    $validated['payment_type'] = 'account_details';
                    // dd($validated);
                    $submitData = InternationalPayment::create($validated);
                } else {
                    $internationalPayment = InternationalPayment::whereId($validated['international_id'])->first();
                    $submitData = $internationalPayment->update($validated);
                }

                $message = empty($validated['international_id']) ? 'Successfully inserted Accout Detail.' : 'Successfully updated Account Detail';
            }

            if (!$submitData) {
                throw new \Exception("Error Processing Request", 1);
            }

            return response()->json(['message' => $message], 200);
        }

        return response()->json(['message' => 'Invalid section or tab submitted.'], 400);
    }
}
