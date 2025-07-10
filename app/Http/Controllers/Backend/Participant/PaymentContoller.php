<?php

namespace App\Http\Controllers\Backend\Participant;

use App\Http\Controllers\Controller;
use App\Models\Conference\ConferenceMemberTypePrice;
use App\Models\Payment\InternationalPayment;
use App\Models\Payment\NationalPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Svg\Tag\Rect;

class PaymentContoller extends Controller
{
    public function fonepay(Request $request, $society, $conference)
    {
        // dd($request->all(), $conference);

        if (is_past($conference->regular_registration_deadline)) {
            return redirect()->back()->with('delete', 'Conference Regisration date has ended.');
        }

        session(['onlinePayment' => $request->all()]);
        $paymentSetting = NationalPayment::where('society_id', $conference->society_id)->first();
        // dd($conference);
        $PID = $paymentSetting->profile_id;
        $MD = 'P';
        $AMT = $request->amount;
        $CRN = 'NPR';
        $DT = date('m/d/Y');
        $PRN = uniqid();
        $R1 = 'test';
        $R2 = 'test';
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
        // dd($conference);
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
        // dd($data);
        // dd($conference);
        if ($data['status'] == 'COMPLETE') {
            $transactionId = $data['transaction_code'];
            // dd($transactionId);
            $amount = (int)$data['total_amount'];
            // dd($amount);
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
        // dd($request->all());
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
        // $amount = 1;
        // dd($amount);
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

        // dd($requestData);
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
        // dd($request);
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

        // dd($queryParams);
        $response = Http::get('https://mpi.moco.com.np/transaction/status', $queryParams);
        // dd($response->json(), $response->status());
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
        // dd($mocoPayment);
        $transactionId = $request->txnID;
        // dd($transactionId);
        $amount = $mocoPayment['amount'];
        return view('backend.participant.conference-registration.payment-success', compact('transactionId', 'amount', 'society', 'conference'));
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
        $form = '<form id="paymentForm" action="https://merchant.conference.nesog.org.np/payment_request.php" method="GET">
                    <input type="hidden" name="formID" value="92921030145569">
                    <input type="hidden" name="api_key" value="de94032bd3aa4d86929a99fc56ec21e8">
                    <input type="hidden" name="merchant_id" value="9104238068">
                    <input type="hidden" name="input_currency" value="USD">
                    <input type="hidden" name="input_amount" value="' . $request->amount . '">
                    <input type="hidden" name="input_3d" value="Y">
                   <input type="hidden" name="success_url" value="' . route('my-society.conference.internationalPaymentResultSuccessProcess', [$society, $conference]) . '">
                     <input type="hidden" name="fail_url" value="' . route('my-society.conference.internationalPaymentResultFail', [$society, $conference]) . '">
                    <input type="hidden" name="cancel_url" value="' . route('my-society.conference.internationalPaymentResultCancel', [$society, $conference]) . '">
                    <input type="hidden" name="backend_url" value="' . route('my-society.conference.internationalPaymentResultBackend', [$society, $conference]) . '">
                    <input type="hidden" name="simple_spc" value="92921030145569">
                </form>
                <script type="text/javascript">document.getElementById("paymentForm").submit();</script>';
        return $form;
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
        // dd($checkPayment);
        return view('backend.participant.conference-registration.create', compact('conference', 'amount', 'memberTypePrice', 'society', 'checkPayment', 'international_payemnt_setting', 'national_payemnt_setting'));
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
        return view('backend.participant.conference-registration.create', compact('conference', 'amount', 'memberTypePrice', 'society', 'checkPayment', 'international_payemnt_setting', 'national_payemnt_setting'));
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
        return view('backend.participant.conference-registration.create', compact('conference', 'amount', 'memberTypePrice', 'society', 'checkPayment', 'international_payemnt_setting', 'national_payemnt_setting'));
    }
}
