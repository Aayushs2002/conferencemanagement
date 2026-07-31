<?php

namespace App\Http\Controllers\Backend\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment\InternationalPayment;
use App\Models\Payment\NationalPayment;
use App\Models\User\Country;
use Exception;
use Illuminate\Http\Request;
 
class PaymentSettingController extends Controller
{
    public function index($society)
    {
        $nationalPayment = NationalPayment::where(['society_id' => $society->id, 'status' => 1])->first();
        $internationalPayment = InternationalPayment::where(['society_id' => $society->id, 'status' => 1,'payment_type' => 'himalayan_bank'])->first();
        $accountDetailsPayment = InternationalPayment::where(['society_id' => $society->id, 'status' => 1, 'payment_type' => 'account_details'])->first();
        $countries = Country::where('status', 1)->orderBy('country_name', 'asc')->get();
        
        // Get selected countries for international payment if exists
        $selectedCountries = [];
        if ($internationalPayment) {
            $selectedCountries = $internationalPayment->countries()->pluck('country_id')->toArray();
        }
        
        // Also get static QR payment setting separately for displaying in the view
        $staticQrPayment = InternationalPayment::where(['society_id' => $society->id, 'status' => 1, 'payment_type' => 'static_qr'])->first();
        $staticQrSelectedCountries = [];
        if ($staticQrPayment) {
            $staticQrSelectedCountries = $staticQrPayment->countries()->pluck('country_id')->toArray();
        }
        
        return view('backend.payment-setting.index', compact('nationalPayment', 'internationalPayment', 'accountDetailsPayment', 'society', 'countries', 'selectedCountries', 'staticQrPayment', 'staticQrSelectedCountries'));
    }
 
    public function store(Request $request, $society) 
    {
        // dd($request->all());
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
            } elseif ($activeTab === 'connectips') {
                $validated = $request->validate([
                    'connectips_merchant_id' => 'required|string|max:255',
                    'connectips_app_id' => 'required|string|max:255',
                    'connectips_app_name' => 'required|string|max:255',
                    'connectips_password' => 'required|string|max:255',
                    'id' => 'nullable',
                ]);

                if (empty($validated['id'])) {
                    $validated['society_id'] = $society->id;
                    $validated['payment_type'] = 'connectips';
                    $submitData = NationalPayment::create($validated);
                } else {
                    $nationalPayment = NationalPayment::whereId($validated['id'])->first();
                    $submitData = $nationalPayment->update($validated);
                }

                $message = empty($validated['id']) ? 'Successfully inserted ConnectIPS payment.' : 'Successfully updated ConnectIPS payment';
            } elseif ($activeTab === 'account_details') {
                $validated = $request->validate([
                    'national_bank_detail' => 'required',
                    'id' => 'nullable'
                ]);

                if (empty($validated['id'])) {
                    $validated['society_id'] = $society->id;
                    $validated['payment_type'] = 'account_details';
                    $validated['account_detail'] = $validated['national_bank_detail'];
                    unset($validated['national_bank_detail']);
                    $submitData = NationalPayment::create($validated);
                } else {
                    $nationalPayment = NationalPayment::whereId($validated['id'])->first();
                    $validated['account_detail'] = $validated['national_bank_detail'];
                    unset($validated['national_bank_detail']);
                    $submitData = $nationalPayment->update($validated);
                }

                $message = empty($validated['id']) ? 'Successfully inserted Account Detail.' : 'Successfully updated Account Detail';
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
                    'encryption_key_id' => 'required',
                    'merchant_signing_private_key' => 'required',
                    'paco_encryption_public_key' => 'required',
                    'merchant_decryption_private_key' => 'required',
                    'paco_signing_public_key' => 'required',
                    'international_id' => 'nullable',
                    'selected_countries' => 'required|array|min:1',
                    'selected_countries.*' => 'exists:countries,id',
                ]);

                if (empty($validated['international_id'])) {
                    $validated['society_id'] = $society->id;
                    $validated['payment_type'] = 'himalayan_bank';
                    // Remove selected_countries from validated data before creating the record
                    $selectedCountries = $validated['selected_countries'];
                    unset($validated['selected_countries']);
                    
                    $submitData = InternationalPayment::create($validated);
                    
                    // Attach selected countries
                    $submitData->countries()->sync($selectedCountries);
                } else {
                    $internationalPayment = InternationalPayment::whereId($validated['international_id'])->first();
                    $selectedCountries = $validated['selected_countries'];
                    unset($validated['selected_countries']);
                    unset($validated['international_id']);
                    
                    $submitData = $internationalPayment->update($validated);
                    
                    // Sync selected countries
                    $internationalPayment->countries()->sync($selectedCountries);
                }

                $message = empty($request->international_id) ? 'Successfully inserted Himalayan Bank payment.' : 'Successfully updated Himalayan Bank payment';
            } else if ($activeTab === 'static_qr') {
                $validated = $request->validate([
                    'static_qr_details' => 'required',
                    'international_id' => 'nullable',
                    'selected_countries_static_qr' => 'required|array|min:1',
                    'selected_countries_static_qr.*' => 'exists:countries,id',
                ]);

                if (empty($validated['international_id'])) {
                    $validated['society_id'] = $society->id;
                    $validated['payment_type'] = 'static_qr';
                    $validated['qr_details'] = $validated['static_qr_details'];
                    // Remove selected_countries from validated data before creating the record
                    $selectedCountries = $validated['selected_countries_static_qr'];
                    unset($validated['selected_countries_static_qr']);
                    unset($validated['static_qr_details']);
                    
                    $submitData = InternationalPayment::create($validated);
                    
                    // Attach selected countries
                    $submitData->countries()->sync($selectedCountries);
                } else {
                    $internationalPayment = InternationalPayment::whereId($validated['international_id'])->first();
                    $internationalPayment->qr_details = $validated['static_qr_details'];
                    $selectedCountries = $validated['selected_countries_static_qr'];
                    
                    $submitData = $internationalPayment->save();
                    
                    // Sync selected countries
                    $internationalPayment->countries()->sync($selectedCountries);
                }

                $message = empty($request->international_id) ? 'Successfully inserted Static QR payment.' : 'Successfully updated Static QR payment';
            } else if ($activeTab === 'account_details') {
                $validated = $request->validate([
                    'bank_detail' => 'required',
                    'international_id' => 'nullable'
                ]);

                $internationalPayment = null;
                if (!empty($validated['international_id'])) {
                    $internationalPayment = InternationalPayment::whereId($validated['international_id'])->first();
                }

                // Fallback to existing account_details record to avoid duplicate rows.
                if (!$internationalPayment) {
                    $internationalPayment = InternationalPayment::where([
                        'society_id' => $society->id,
                        'status' => 1,
                        'payment_type' => 'account_details'
                    ])->first();
                }

                if ($internationalPayment) {
                    $submitData = $internationalPayment->update([
                        'bank_detail' => $validated['bank_detail']
                    ]);
                } else {
                    $submitData = InternationalPayment::create([
                        'society_id' => $society->id,
                        'payment_type' => 'account_details',
                        'bank_detail' => $validated['bank_detail']
                    ]);
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
