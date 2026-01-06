<?php

namespace App\Http\Controllers\Backend\Participant;

use App\Http\Controllers\Controller;
use App\Models\Conference\ConferenceAddon;
use App\Models\Conference\ConferenceMemberTypePrice;
use App\Models\Payment\InternationalPayment;
use App\Models\Payment\NationalPayment;
use App\Models\Workshop\Workshop;
use App\Services\HBL\Api\Payment;
use App\Services\ConnectIPSService;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Svg\Tag\Rect;

class PaymentContoller extends Controller
{
    public function fonepay(Request $request, $society, $conference)
    {
        // dd($request->all());
        if (is_past($conference->regular_registration_deadline)) {
            return redirect()->back()->with('delete', 'Conference Regisration date has ended.');
        }

        session(['onlinePayment' => $request->all()]);
        $paymentSetting = NationalPayment::where('society_id', $conference->society_id)->first();
        $PID = $paymentSetting->profile_id;
        $MD = 'P';
        $AMT = $request->amount;
        $CRN = 'NPR';
        $DT = date('m/d/Y');
        $PRN = uniqid();
        $R1 = $conference->conference_name;
        $R2 = $conference->conference_name;
        $RU = route('my-society.conference.fonePaySuccess', [$society, $conference]);
        $sharedSecretKey = $paymentSetting->secret_key;
        $DV = hash_hmac('sha512', $PID . ',' . $MD . ',' . $PRN . ',' . $AMT . ',' . $CRN . ',' . $DT . ',' . $R1 . ',' . $R2 . ',' . $RU, $sharedSecretKey);

        $form = '<form id="paymentForm" action="https://clientapi.fonepay.com/api/merchantRequest" method="GET">
                    <input type="hidden" name="PID" value="' . $PID . '">
                    <input type="hidden" name="MD" value="' . $MD . '">
                    <input type="hidden" name="AMT" value="' . $AMT . '">
                    <input type="hidden" name="CRN" value="' . $CRN . '">
                    <input type="hidden" name="DT" value="' . $DT . '">
                    <input type="hidden" name="R1" value="' . $R1 . '">
                    <input type="hidden" name="R2" value="' . $R2 . '">
                    <input type="hidden" name="DV" value="' . $DV . '">
                    <input type="hidden" name="RU" value="' . $RU . '">
                    <input type="hidden" name="PRN" value="' . $PRN . '">
                </form>
                <script type="text/javascript">document.getElementById("paymentForm").submit();</script>';

        return $form;
    }

    public function fonePaySuccess(Request $request, $society, $conference)
    {
        if ($request->RC == 'failed' || $request->RC == 'cancel') {
            return redirect()->route('my-society.conference.create', [$society, $conference])->with('delete', 'Payment process has been failed or cancelled, please try again.');
        } else {
            $transactionId = $request->UID;
            $amount = $request->P_AMT;
            $national_payemnt_setting = NationalPayment::where('society_id', $conference->society_id)->first();
            $international_payemnt_setting = InternationalPayment::where('society_id', $conference->society_id)->first();
            return view('backend.participant.conference-registration.payment-success', compact('transactionId', 'amount', 'society', 'conference', 'national_payemnt_setting', 'international_payemnt_setting'));
        }
    }

    public function esewa(Request $request, $society, $conference)
    {
        if (is_past($conference->regular_registration_deadline)) {
            return redirect()->back()->with('delete', 'Conference Regisration date has ended.');
        }

        session(['onlinePayment' => $request->all()]);
        $paymentSetting = NationalPayment::where('society_id', $conference->society_id)->select('esewa_product_code', 'esewa_secret_key')->first();
        $transaction_uuid = uniqid();
        $amount = $request->amount;
        $total_amount = $amount;
        $product_code = $paymentSetting->esewa_product_code;
        $secretKey = $paymentSetting->esewa_secret_key;

        $message = "total_amount={$total_amount},transaction_uuid={$transaction_uuid},product_code={$product_code}";
        $s = hash_hmac('sha256', $message, $secretKey, true);
        $signature = base64_encode($s);

        $form = '
        <html>
        <head><title>Redirecting to eSewa...</title></head>
        <body onload="document.forms[\'esewaPaymentForm\'].submit();">
            <form id="esewaPaymentForm" action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST">
                <input type="hidden" name="amount" value="' . $amount . '">
                <input type="hidden" name="tax_amount" value="0">
                <input type="hidden" name="total_amount" value="' . $total_amount . '">
                <input type="hidden" name="transaction_uuid" value="' . $transaction_uuid . '">
                <input type="hidden" name="product_code" value="' . $product_code . '">
                <input type="hidden" name="product_service_charge" value="0">
                <input type="hidden" name="product_delivery_charge" value="0">
                <input type="hidden" name="success_url" value="' . route('my-society.conference.esewaSuccess', [$society, $conference]) . '">
                <input type="hidden" name="failure_url" value="' . route('my-society.conference.esewaError', [$society, $conference]) . '">
                <input type="hidden" name="signed_field_names" value="total_amount,transaction_uuid,product_code">
                <input type="hidden" name="signature" value="' . $signature . '">
            </form>
        </body>
        </html>
    ';
        return response($form);
    }

    public function esewaSuccess(Request $request, $society, $conference)
    {
        $data = base64_decode($request->data);
        $data = json_decode($data, true);
        if ($data['status'] == 'COMPLETE') {
            $transactionId = $data['transaction_code'];
            $amount = (int)$data['total_amount'];
            return view('backend.participant.conference-registration.payment-success', compact('transactionId', 'amount', 'society', 'conference'));
        } else {
            return redirect()->route('my-society.conference.create', [$society, $conference])->with('delete', 'Payment process has been failed or cancelled, please try again.');
        }
    }

    public function esewaError(Request $request, $society, $conference)
    {
        return redirect()->route('my-society.conference.create', [$society, $conference])->with('delete', 'Payment process has been failed or cancelled, please try again.');
    }


    public function khalti(Request $request, $society, $conference)
    {
        if (is_past($conference->regular_registration_deadline)) {
            return redirect()->back()->with('delete', 'Conference Regisration date has ended.');
        }

        session(['onlinePayment' => $request->all()]);
        $paymentSetting = NationalPayment::where('society_id', $conference->society_id)->select('khalti_live_secret_key')->first();
        $amount = $request->amount;
        $customer_name = current_user()->f_name . ' ' . current_user()->m_name . ' ' . current_user()->l_name;
        $customer_email = current_user()->email;
        $customer_phone = current_user()->userDetail->phone;
        $configs = [
            "return_url" => route('my-society.conference.khaltiSuccess', [$society, $conference]),
            "website_url" => config('app.url'),
            "amount" =>  $amount * 100,
            "purchase_order_id" => uniqid(),
            "purchase_order_name" => $conference->conference_name,
            "customer_info" => [
                "name" => $customer_name,
                "email" => $customer_email,
                "phone" => $customer_phone
            ]
        ];

        $json_configs = json_encode($configs);

        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://dev.khalti.com/api/v2/epayment/initiate/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $json_configs,
                CURLOPT_HTTPHEADER => array(
                    // 'Authorization: Key live_secret_key_68791341fdd94846a146f0457ff7b455',
                    'Authorization: Key ' . $paymentSetting->khalti_live_secret_key,
                    'Content-Type: application/json',
                ),
            )
        );
        $response = curl_exec($curl);

        curl_close($curl);

        if ($response) {
            $data = json_decode($response);
            return redirect($data->payment_url);
        }
    }

    public function khaltiSuccess(Request $request, $society, $conference)
    {
        $data = $request->all();
        if ($data['status'] == 'Completed') {
            $transactionId = $data['transaction_id'];
            $amount = (int)($data['total_amount'] / 100);
            return view('backend.participant.conference-registration.payment-success', compact('transactionId', 'amount', 'society', 'conference'));
        } else {
            return redirect()->route('my-society.conference.create', [$society, $conference])->with('delete', 'Payment process has been failed or cancelled, please try again.');
        }
    }


    public function moco(Request $request, $society, $conference)
    {
        if (is_past($conference->regular_registration_deadline)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Conference Registration date has ended.'
            ], 500);
        }
        session(['onlinePayment' => $request->all()]);

        $paymentSetting = NationalPayment::where('society_id', $conference->society_id)->select('moco_merchant_id', 'moco_outlet_id', 'moco_terminal_id', 'moco_shared_key')->first();
        // MoCo API Configuration
        $mid = $paymentSetting->moco_merchant_id;
        $oid = $paymentSetting->moco_outlet_id;
        $tid = $paymentSetting->moco_terminal_id;
        $amount = $request->amount;
        $referenceNumber = uniqid();
        $timestamp = now()->utc()->format('Y-m-d H:i:s');
        $sharedSecretKey = $paymentSetting->moco_shared_key;


        $hashData = $mid . $oid . $tid . $timestamp . $referenceNumber . $amount;
        $hash = hash_hmac('sha256', $hashData, $sharedSecretKey);

        $requestData = [
            "mid" => $mid,
            "oid" => $oid,
            "tid" => $tid,
            "amount" => $amount,
            "referenceNumber" => $referenceNumber,
            "timestamp" => $timestamp,
            "format" => "image",
            "hash" => $hash
        ];

        try {
            $response = Http::timeout(30)
                ->post('https://mpi.moco.com.np/transaction/qr', $requestData);

            if ($response->successful()) {
                $responseBody = $response->body();
                $contentType = $response->header('Content-Type');

                $qrData = null;
                $responseData = null;

                if (strpos($contentType, 'application/json') !== false || strpos($contentType, 'text/json') !== false) {
                    $responseData = $response->json();

                    if ($responseData) {
                        if (isset($responseData['qr'])) {
                            $qrData = $responseData['qr'];
                        } elseif (isset($responseData['image'])) {
                            $qrData = $responseData['image'];
                        } elseif (isset($responseData['data'])) {
                            $qrData = $responseData['data'];
                        } elseif (isset($responseData['qrCode'])) {
                            $qrData = $responseData['qrCode'];
                        } elseif (isset($responseData['qr_code'])) {
                            $qrData = $responseData['qr_code'];
                        }
                    }
                } elseif (strpos($contentType, 'image/') !== false) {
                    $qrData = 'data:' . $contentType . ';base64,' . base64_encode($responseBody);
                    $responseData = ['type' => 'image', 'format' => $contentType];
                } elseif (base64_decode($responseBody, true) !== false && strlen($responseBody) > 100) {
                    $qrData = 'data:image/png;base64,' . $responseBody;
                    $responseData = ['type' => 'base64_image'];
                } else {
                    $qrData = $responseBody;
                    $responseData = ['type' => 'raw_data', 'content_type' => $contentType];
                }

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'referenceNumber' => $referenceNumber,
                        'amount' => $amount,
                        'timestamp' => $timestamp,
                        'qr_data' => $qrData,
                        'response_info' => $responseData,
                        'content_type' => $contentType
                    ]
                ]);
            } else {

                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to generate QR code',
                    'error_code' => $response->status()
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to connect to payment gateway'
            ], 500);
        }
    }

    public function mocoCheckStatus(Request $request, $society, $conference)
    {
        $paymentSetting = NationalPayment::where('society_id', $conference->society_id)->select('moco_merchant_id', 'moco_outlet_id', 'moco_terminal_id', 'moco_shared_key')->first();

        $mid = $paymentSetting->moco_merchant_id;
        $oid = $paymentSetting->moco_outlet_id;
        $tid = $paymentSetting->moco_terminal_id;

        $referenceNumber = $request->reference_number;
        // $referenceNumber = '685b90bb5a193';
        $timestamp = now('UTC')->format('Y-m-d H:i:s');
        $localTxnDate = now('UTC')->format('Y-m-d');

        $sharedKey = $paymentSetting->moco_shared_key;
        $hash = hash_hmac('sha256', $mid . $oid . $tid . $timestamp . $referenceNumber, $sharedKey);

        $queryParams = [
            'mid' => $mid,
            'oid' => $oid,
            'tid' => $tid,
            'referenceNumber' => $referenceNumber,
            'localTxnDate' => $localTxnDate,
            'timestamp' => $timestamp,
            'hash' => $hash
        ];

        $response = Http::get('https://mpi.moco.com.np/transaction/status', $queryParams);
        return response()->json($response->json(), $response->status());
        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Transaction completed successfully',
        //     'txnStatus' => 'success',
        //     'txnID' => 'test12345',
        //     'referenceNumber' => $request->reference_number
        // ]);
    }

    public function mocoSuccess(Request $request, $society, $conference)
    {
        $mocoPayment = session()->get('onlinePayment');
        $transactionId = $request->txnID;
        $amount = $mocoPayment['amount'];
        return view('backend.participant.conference-registration.payment-success', compact('transactionId', 'amount', 'society', 'conference'));
    }


    public function connectips(Request $request, $society, $conference)
    {
        if (is_past($conference->regular_registration_deadline)) {
            return redirect()->back()->with('delete', 'Conference Registration date has ended.');
        }

        try {
            // Store payment data in session
            session(['onlinePayment' => $request->all()]);
            // dd($request->all());
            // Initialize ConnectIPS Service
            $connectIPSService = new ConnectIPSService($society);

            // Generate unique transaction ID and reference ID
            $txnId = 'CONF-' . $conference->id . '-' . time();
            $referenceId = 'REF-' . time() . '-' . rand(1000, 9999);
            $txnDate = date('d-m-Y');
            $amount = $request->amount;

            // Prepare payment data
            $paymentData = [
                'txnId' => $txnId,
                'txnDate' => $txnDate,
                'txnAmt' => $amount,
                'referenceId' => $referenceId,
                'remarks' => 'Conference Registration: ' . substr($conference->conference_name, 0, 50),
                'particulars' => 'Registration for ' . current_user()->fullName(current_user()),
            ];

            // Get prepared form data with token
            $formData = $connectIPSService->preparePaymentData($paymentData);

            // Store transaction details in session for validation
            session([
                'connectips_reference_id' => $referenceId,
                'connectips_txn_id' => $txnId,
                'connectips_amount' => $amount,
            ]);

            Log::info('ConnectIPS Conference Payment Initiated', [
                'conference_id' => $conference->id,
                'user_id' => current_user()->id,
                'txn_id' => $txnId,
                'reference_id' => $referenceId,
                'amount' => $amount,
            ]);

            // Return view with auto-submit form
            return view('backend.participant.conference-registration.connectips-redirect', [
                'formData' => $formData,
                'conference' => $conference,
            ]);

        } catch (Exception $e) {
            Log::error('ConnectIPS Conference Payment Failed: ' . $e->getMessage(), [
                'conference_id' => $conference->id,
                'user_id' => current_user()->id,
                'error' => $e->getMessage(),
            ]);
            
            return redirect()->back()->with('delete', 'Failed to initiate ConnectIPS payment: ' . $e->getMessage());
        }
    }

    public function connectipsSuccess(Request $request, $society, $conference)
    {
        try {
            // Get transaction data from callback
            $txnId = $request->input('TXNID');
            $status = $request->input('STATUS');
            $message = $request->input('MESSAGE');
            
            Log::info('ConnectIPS Success Callback', [
                'txn_id' => $txnId,
                'status' => $status,
                'message' => $message,
                'all_params' => $request->all(),
            ]);

            // Get stored data from session
            $storedTxnId = session('connectips_txn_id');
            $referenceId = session('connectips_reference_id');
            $amount = session('connectips_amount');
            
            Log::info('ConnectIPS Session Data', [
                'stored_txn_id' => $storedTxnId,
                'reference_id' => $referenceId,
                'amount' => $amount,
            ]);

            // If callback TXNID exists, use it for validation
            $validationTxnId = !empty($txnId) ? $txnId : $storedTxnId;
            
            if (!$validationTxnId) {
                throw new Exception('Transaction ID not found in callback or session');
            }

            if (!$referenceId || !$amount) {
                throw new Exception('Transaction data not found in session. Please try again.');
            }

            // If status is already provided and successful, proceed without validation
            if (!empty($status) && (strtoupper($status) === 'SUCCESS' || strtoupper($status) === 'COMPLETED')) {
                Log::info('ConnectIPS Payment Successful from Callback Status', [
                    'txn_id' => $validationTxnId,
                    'reference_id' => $referenceId,
                    'amount' => $amount,
                    'status' => $status,
                ]);

                // Clear session data
                session()->forget(['connectips_txn_id', 'connectips_reference_id', 'connectips_amount']);

                // Get payment settings for success page
                $national_payemnt_setting = NationalPayment::where('society_id', $conference->society_id)->first();
                $international_payemnt_setting = InternationalPayment::where('society_id', $conference->society_id)->first();

                // Use ConnectIPS transaction ID
                $transactionId = $validationTxnId;

                return view('backend.participant.conference-registration.payment-success', 
                    compact('transactionId', 'amount', 'society', 'conference', 'national_payemnt_setting', 'international_payemnt_setting'));
            }

            // Initialize ConnectIPS Service for validation
            $connectIPSService = new ConnectIPSService($society);

            // Add a small delay to allow ConnectIPS to process the transaction
            sleep(2);

            // Try validation with retry mechanism
            $maxRetries = 3;
            $retryDelay = 2; // seconds
            $validationResponse = null;
            $validationSuccess = false;

            for ($i = 0; $i < $maxRetries; $i++) {
                try {
                    Log::info("ConnectIPS Validation Attempt " . ($i + 1), [
                        'txn_id' => $validationTxnId,
                        'reference_id' => $referenceId,
                        'amount' => $amount,
                    ]);
                    // Validate the transaction with ConnectIPS using transaction ID
                    $validationResponse = $connectIPSService->validateTransaction($validationTxnId, $amount);

                    Log::info('ConnectIPS Validation Response', [
                        'attempt' => ($i + 1),
                        'txn_id' => $validationTxnId,
                        'response' => $validationResponse,
                    ]);

                    // Check if transaction was successful
                    if ($connectIPSService->isTransactionSuccessful($validationResponse)) {
                        $validationSuccess = true;
                        break;
                    }

                    // If transaction not found, wait and retry
                    if (isset($validationResponse['statusDesc']) && 
                        (stripos($validationResponse['statusDesc'], 'not found') !== false || 
                         stripos($validationResponse['statusDesc'], 'pending') !== false)) {
                        
                        if ($i < $maxRetries - 1) {
                            Log::info("Transaction not found or pending, retrying in {$retryDelay} seconds...");
                            sleep($retryDelay);
                            continue;
                        }
                    }

                    break;

                } catch (Exception $e) {
                    Log::warning("ConnectIPS Validation Attempt " . ($i + 1) . " Failed", [
                        'error' => $e->getMessage(),
                    ]);
                    
                    if ($i < $maxRetries - 1) {
                        sleep($retryDelay);
                        continue;
                    }
                    throw $e;
                }
            }

            if ($validationSuccess) {
                // Get detailed transaction information
                try {
                    $transactionDetails = $connectIPSService->getTransactionDetails($validationTxnId, $amount);
                    
                    Log::info('ConnectIPS Payment Successful', [
                        'txn_id' => $validationTxnId,
                        'reference_id' => $referenceId,
                        'amount' => $amount,
                        'details' => $transactionDetails,
                    ]);
                } catch (Exception $e) {
                    Log::warning('Failed to get transaction details, but payment validated', [
                        'error' => $e->getMessage(),
                    ]);
                }

                // Clear session data
                session()->forget(['connectips_txn_id', 'connectips_reference_id', 'connectips_amount']);

                // Get payment settings for success page
                $national_payemnt_setting = NationalPayment::where('society_id', $conference->society_id)->first();
                $international_payemnt_setting = InternationalPayment::where('society_id', $conference->society_id)->first();

                // Use ConnectIPS transaction ID
                $transactionId = $validationTxnId;

                return view('backend.participant.conference-registration.payment-success', 
                    compact('transactionId', 'amount', 'society', 'conference', 'national_payemnt_setting', 'international_payemnt_setting'));
            } else {
                Log::warning('ConnectIPS Payment Validation Failed After Retries', [
                    'txn_id' => $validationTxnId,
                    'validation_response' => $validationResponse,
                ]);
                
                $errorMessage = 'Payment verification failed. ';
                if (isset($validationResponse['statusDesc'])) {
                    $errorMessage .= $validationResponse['statusDesc'];
                } else {
                    $errorMessage .= 'Transaction could not be verified. Please contact support with transaction ID: ' . $validationTxnId;
                }
                
                return redirect()->route('my-society.conference.create', [$society, $conference])
                    ->with('delete', $errorMessage);
            }

        } catch (Exception $e) {
            Log::error('ConnectIPS Success Callback Failed: ' . $e->getMessage(), [
                'conference_id' => $conference->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);
            
            $errorMessage = 'Payment verification error: ' . $e->getMessage();
            if (!empty($txnId)) {
                $errorMessage .= ' (Transaction ID: ' . $txnId . ')';
            }
            
            return redirect()->route('my-society.conference.create', [$society, $conference])
                ->with('delete', $errorMessage);
        }
    }

    public function connectipsFailure(Request $request, $society, $conference)
    {
        $txnId = $request->input('TXNID');
        
        Log::info('ConnectIPS Failure Callback', [
            'txn_id' => $txnId,
            'all_params' => $request->all(),
        ]);

        // Clear session data
        session()->forget(['connectips_txn_id', 'connectips_reference_id', 'connectips_amount']);

        return redirect()->route('my-society.conference.create', [$society, $conference])
            ->with('delete', 'ConnectIPS payment has been cancelled or failed. Please try again.');
    }


    // public function internationalPayment(Request $request, $society, $conference)
    // {
    //     // dd($request->all()); 
    //     session(['onlinePayment' => $request->all()]);

    //     $paymentSetting = InternationalPayment::where('society_id', $society->id)->first();
    //     $form = '<form id="paymentForm" action="http://merchant.conference.san.org.np/payment_request.php" method="POST">
    //                 <input type="hidden" name="formID" value="92921030145569">
    //                 <input type="hidden" name="api_key" value="' . $paymentSetting->api_key . '">
    //                 <input type="hidden" name="merchant_id" value="' . $paymentSetting->merchant_key . '">
    //                 <input type="hidden" name="input_currency" value="USD">
    //                 <input type="hidden" name="input_amount" value="' . $request->amount . '">
    //                 <input type="hidden" name="input_3d" value="Y">
    //                 <input type="hidden" name="success_url" value="' . route('my-society.conference.internationalPaymentResultSuccessProcess', [$society, $conference]) . '">
    //                 <input type="hidden" name="fail_url" value="' . route('my-society.conference.internationalPaymentResultFail', [$society, $conference]) . '">
    //                 <input type="hidden" name="cancel_url" value="' . route('my-society.conference.internationalPaymentResultCancel', [$society, $conference]) . '">
    //                 <input type="hidden" name="backend_url" value="' . route('my-society.conference.internationalPaymentResultBackend', [$society, $conference]) . '">
    //                 <input type="hidden" name="simple_spc" value="92921030145569">
    //             </form>
    //             <script type="text/javascript">document.getElementById("paymentForm").submit();</script>';
    //     return $form;
    // }

    // public function internationalPayment(Request $request, $society, $conference)
    // {
    //     if (is_past($conference->regular_registration_deadline)) {
    //         return redirect()->back()->with('delete', 'Conference Regisration date has ended.');
    //     }
    //     session(['onlinePayment' => $request->all()]);

    //     $paymentSetting = InternationalPayment::where('society_id', $society->id)->first();
    //     $form = '<form id="paymentForm" action="https://localhost/hbldemo/payment_request.php" method="POST">
    //                 <input type="hidden" name="formID" value="92921030145569">
    //                 <input type="hidden" name="api_key" value="' . $paymentSetting->api_key . '">
    //                 <input type="hidden" name="merchant_id" value="' . $paymentSetting->merchant_key . '">
    //                 <input type="hidden" name="AccessToken" value="' . $paymentSetting->access_token . '">
    //                 <input type="hidden" name="MerchantSigningPrivateKey" value="' . $paymentSetting->merchant_signing_private_key . '">
    //                 <input type="hidden" name="PacoEncryptionPublicKey" value="' . $paymentSetting->paco_encryption_public_key . '">
    //                 <input type="hidden" name="MerchantDecryptionPrivateKey" value="' . $paymentSetting->merchant_decryption_private_key . '">
    //                 <input type="hidden" name="PacoSigningPublicKey" value="' . $paymentSetting->paco_signing_public_key . '">
    //                 <input type="hidden" name="input_currency" value="USD">
    //                 <input type="hidden" name="input_amount" value="' . $request->amount . '"> 
    //                 <input type="hidden" name="input_3d" value="Y">
    //                  <input type="hidden" name="success_url" value="' . route('my-society.conference.internationalPaymentResultSuccessProcess', [$society, $conference]) . '">
    //                  <input type="hidden" name="fail_url" value="' . route('my-society.conference.internationalPaymentResultFail', [$society, $conference]) . '">
    //                 <input type="hidden" name="cancel_url" value="' . route('my-society.conference.internationalPaymentResultCancel', [$society, $conference]) . '">
    //                 <input type="hidden" name="backend_url" value="' . route('my-society.conference.internationalPaymentResultBackend', [$society, $conference]) . '">
    //                 <input type="hidden" name="simple_spc" value="92921030145569">
    //             </form>
    //             <script type="text/javascript">document.getElementById("paymentForm").submit();</script>';
    //     return $form;
    // }
    public function internationalPayment(Request $request, $society, $conference)
    {

        if (is_past($conference->regular_registration_deadline)) {
            return redirect()->back()->with('delete', 'Conference Regisration date has ended.');
        }
        session(['onlinePayment' => $request->all()]);

        // $paymentSetting = InternationalPayment::where('society_id', $society->id)->first();
        // $form = '<form id="paymentForm" action="https://merchant.conference.nesog.org.np/payment_request.php" method="GET">
        //             <input type="hidden" name="formID" value="92921030145569">
        //             <input type="hidden" name="api_key" value="de94032bd3aa4d86929a99fc56ec21e8">
        //             <input type="hidden" name="merchant_id" value="9104238068">
        //             <input type="hidden" name="input_currency" value="USD">
        //             <input type="hidden" name="input_amount" value="' . $request->amount . '">
        //             <input type="hidden" name="input_3d" value="Y">
        //            <input type="hidden" name="success_url" value="' . route('my-society.conference.internationalPaymentResultSuccessProcess', [$society, $conference]) . '">
        //              <input type="hidden" name="fail_url" value="' . route('my-society.conference.internationalPaymentResultFail', [$society, $conference]) . '">
        //             <input type="hidden" name="cancel_url" value="' . route('my-society.conference.internationalPaymentResultCancel', [$society, $conference]) . '">
        //             <input type="hidden" name="backend_url" value="' . route('my-society.conference.internationalPaymentResultBackend', [$society, $conference]) . '">
        //             <input type="hidden" name="simple_spc" value="92921030145569">
        //         </form>
        //         <script type="text/javascript">document.getElementById("paymentForm").submit();</script>';
        // return $form;
        $paymentSetting = InternationalPayment::where('society_id', $society->id)->first();
        // dd($paymentSetting);
        try {
            $payment = new Payment();
            $joseResponse = $payment->ExecuteFormJose(
                $paymentSetting->merchant_key, // merchant_id
                $paymentSetting->api_key, // api_key
                'USD', // input_currency
                $request->amount,   // input_amount
                'Y',   // input_3d 
                route('my-society.conference.internationalPaymentResultSuccessProcess', [$society, $conference]), // success_url
                route('my-society.conference.internationalPaymentResultFail', [$society, $conference]),  // fail_url
                route('my-society.conference.internationalPaymentResultCancel', [$society, $conference]),  // cancel_url
                route('my-society.conference.internationalPaymentResultBackend', [$society, $conference]), // backend_url
                $paymentSetting->access_token,
                $paymentSetting->merchant_signing_private_key,
                $paymentSetting->paco_encryption_public_key,
                $paymentSetting->merchant_decryption_private_key,
                $paymentSetting->paco_signing_public_key
            );
            // $joseResponse = $payment->ExecuteFormJose(
            //     '9104137120', // merchant_id
            //     '65805a1636c74b8e8ac81a991da80be4', // api_key
            //     'NPR', // input_currency
            //     '1',   // input_amount
            //     'N',   // input_3d
            //     'http://127.0.0.1:9090/payment/success', // success_url
            //     'http://127.0.0.1:9090/payment/failed',  // fail_url
            //     'http://127.0.0.1:9090/payment/cancel',  // cancel_url
            //     'http://127.0.0.1:9090/payment/callback' // backend_url
            // );

            $response_obj = json_decode($joseResponse);
            header("Location: " . $response_obj->response->Data->paymentPage->paymentPageURL);
            exit();
        } catch (GuzzleException $e) {
            echo '\n Message: ' . $e->getMessage();
            echo '\n Trace: ' . $e->getTraceAsString();
        } catch (Exception $e) {
            echo '\n Message: ' . $e->getMessage();
            echo '\n Trace: ' . $e->getTraceAsString();
        }
    }


    public function internationalPaymentResultSuccessProcess(Request $request, $society, $conference)
    {
        $orderNo  = $request->orderNo;
        // $inquiry = 'https://merchant.omwaytechnologies.com/inquiry_request.php?orderno=' . $orderNo;
        $inquiry = 'https://merchant.conference.nesog.org.np/inquiry_request.php?orderno=' . $orderNo;

        return redirect($inquiry);
    }

    public function internationalPaymentResultSuccess(Request $request, $society, $conference)
    {
        $data = $request->query('data');

        $decodedData = urldecode($data);

        $responseObject = json_decode($decodedData);
        $transactionId = $responseObject->response->Data[0]->PspReferenceNo;
        return view('backend.participant.conference-registration.payment-success', compact('transactionId'));
    }

    public function internationalPaymentResultFail(Request $request, $society, $conference)
    {
        // dd($request);
        $checkPayment = 'failed';
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
        $international_payemnt_setting = InternationalPayment::where('society_id', $conference->society_id)->first();
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
        $conferenceAddons = ConferenceAddon::where(['conference_id' => $conference->id, 'status' => 1])->get();

        // dd($checkPayment);
        return view('backend.participant.conference-registration.create', compact('workshops', 'conferenceAddons', 'conference', 'amount', 'memberTypePrice', 'society', 'checkPayment', 'international_payemnt_setting', 'national_payemnt_setting'));
        // $transactionId = $request->orderNo;
        // return view('backend.conferences.registrations.international-payment-success', compact('transactionId'));
    }

    public function internationalPaymentResultCancel(Request $request, $society, $conference)
    {
        $checkPayment = 'cancelled';
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
        $international_payemnt_setting = InternationalPayment::where('society_id', $conference->society_id)->first();
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
        $conferenceAddons = ConferenceAddon::where(['conference_id' => $conference->id, 'status' => 1])->get();
        return view('backend.participant.conference-registration.create', compact('workshops', 'conferenceAddons', 'conference', 'amount', 'memberTypePrice', 'society', 'checkPayment', 'international_payemnt_setting', 'national_payemnt_setting'));
    }

    public function internationalPaymentResultBackend($society, $conference)
    {
        $checkPayment = 'terminated';
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
        $international_payemnt_setting = InternationalPayment::where('society_id', $conference->society_id)->first();
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
        $conferenceAddons = ConferenceAddon::where(['conference_id' => $conference->id, 'status' => 1])->get();
        return view('backend.participant.conference-registration.create', compact('workshops', 'conferenceAddons', 'conference', 'amount', 'memberTypePrice', 'society', 'checkPayment', 'international_payemnt_setting', 'national_payemnt_setting'));
    }
}
