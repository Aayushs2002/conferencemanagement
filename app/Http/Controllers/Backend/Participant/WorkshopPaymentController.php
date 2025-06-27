<?php

namespace App\Http\Controllers\Backend\Participant;

use App\Http\Controllers\Controller;
use App\Models\Conference\Conference;
use App\Models\Payment\InternationalPayment;
use App\Models\Payment\NationalPayment;
use App\Models\Workshop\Workshop;
use App\Models\Workshop\WorkshopRegistration;
use Illuminate\Http\Request;

class WorkshopPaymentController extends Controller
{
    public function fonePay(Request $request, $society, $conference, $workshop)
    {
        $data = [
            'id' => $workshop->id,
            'price' => $request->price,
            'payment_type' => 1
        ];
        session(['workshopPayment' => $data]);
        $paymentSetting = NationalPayment::where('society_id', $conference->society_id)->first();
        $PID = $paymentSetting->profile_id;
        $MD = 'P';
        $AMT = $request->price;
        $CRN = 'NPR';
        $DT = date('m/d/Y');
        $PRN = uniqid();
        $R1 = 'test';
        $R2 = 'test';
        $RU = route('my-society.conference.workshop-registration.fonePaySuccess', [$society, $conference]);
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
        // if ($request->RC == 'failed' || $request->RC == 'cancel') {
        //     return redirect()->route('my-society.conference.workshop.index', $conference)->with('delete', 'Payment process has been failed or cancelled, please try again.');
        // } else {
        $transactionId = $request->UID;
        $amount = $request->P_AMT;
        $sessionData = session()->get('workshopPayment');
        $workshop = Workshop::whereId($sessionData['id'])->first();
        $paymetType = $sessionData['payment_type'];
        return view('backend.participant.workshop-registration.payment-success', compact('transactionId', 'amount', 'workshop', 'society', 'conference', 'paymetType'));
        // }
    }


    public function internationalPayment($id, $price)
    {
        $data = [
            'workshop_id' => $id,
            'amount' => $price,
            'payment_type' => 2
        ];

        session(['workshopPayment' => $data]);
        $user  = auth()->user();
        $society_id = $user->userDetail->society_id;

        $paymentSetting = InternationalPayment::where('user_id', $society_id)->first();
        // $form = '<form id="paymentForm" action="https://merchant.omwaytechnologies.com/payment_request.php" method="GET">
        $form = '<form id="paymentForm" action="http://merchant.conference.san.org.np/payment_request.php" method="GET">
                    <input type="hidden" name="formID" value="92921030145569">
                    <input type="hidden" name="api_key" value="' . $paymentSetting->api_key . '">
                    <input type="hidden" name="merchant_id" value="' . $paymentSetting->merchant_key . '">
                    <input type="hidden" name="input_currency" value="USD">
                    <input type="hidden" name="input_amount" value="' . $price . '">
                    <input type="hidden" name="input_3d" value="Y">
                    <input type="hidden" name="success_url" value="' . route('workshop-registration.internationalPaymentResultSuccessProcess') . '">
                    <input type="hidden" name="fail_url" value="' . route('workshop-registration.internationalPaymentResultFail') . '">
                    <input type="hidden" name="cancel_url" value="' . route('workshop-registration.internationalPaymentResultCancel') . '">
                    <input type="hidden" name="backend_url" value="' . route('workshop-registration.internationalPaymentResultBackend') . '">
                    <input type="hidden" name="simple_spc" value="92921030145569">
                </form>
                <script type="text/javascript">document.getElementById("paymentForm").submit();</script>';
        return $form;
    }

    public function internationalPaymentResultSuccessProcess(Request $request)
    {
        $orderNo  = $request->orderNo;
        $inquiry = 'https://merchant.omwaytechnologies.com/inquiry_request_workshop.php?orderno=' . $orderNo;
        return redirect($inquiry);
    }

    public function internationalPaymentResultSuccess(Request $request)
    {
        $data = $request->query('data');

        // Decode the URL-encoded string
        $decodedData = urldecode($data);

        $responseObject = json_decode($decodedData);
        $transactionId = $responseObject->response->Data[0]->PspReferenceNo;
        $sessionData = session()->get('workshopPayment');
        $workshop = Workshop::whereSlug($sessionData['id'])->first();
        $amount = $sessionData['amount'];
        return view('payment.workshop-payment-success', compact('transactionId', 'amount', 'workshop'));
    }



    public function internationalPaymentResultCancel(Request $request)
    {
        $checkPayment = 'cancelled';
        $workshops = Workshop::where(['approved_status' => 1, 'status' => 1])->whereNot('user_id', auth()->user()->id)->get();
        $registrations = WorkshopRegistration::where(['user_id' => auth()->user()->id, 'status' => 1])->get();
        return view('workshops.registrations.show', compact('registrations', 'workshops', 'checkPayment'));
    }

    public function internationalPaymentResultBackend()
    {
        $checkPayment = 'terminated';
        $latestConference = Conference::latestConference();
        $workshops = Workshop::where(['conference_id' => $latestConference->id, 'approved_status' => 1, 'status' => 1])->whereNot('user_id', auth()->user()->id)->get();
        $registrations = WorkshopRegistration::where(['user_id' => auth()->user()->id, 'status' => 1])->get();
        return view('workshops.registrations.show', compact('registrations', 'workshops', 'checkPayment'));
    }

    public function internationalPaymentResultFail(Request $request)
    {
        $checkPayment = 'failed';
        $latestConference = Conference::latestConference();
        $workshops = Workshop::where(['conference_id' => $latestConference->id, 'approved_status' => 1, 'status' => 1])->whereNot('user_id', auth()->user()->id)->get();
        $registrations = WorkshopRegistration::where(['user_id' => auth()->user()->id, 'status' => 1])->get();
        return view('workshops.registrations.show', compact('registrations', 'workshops', 'checkPayment'));
        // $transactionId = $request->orderNo;
        // return view('backend.workshops.registrations.international-payment-success', compact('transactionId'));
    }
}
