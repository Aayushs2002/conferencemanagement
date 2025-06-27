<?php

namespace App\Http\Controllers;

use App\Models\User\MemberType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CommonController extends Controller
{
    public function convertUsdToInr(Request $request)
    {
        try {
            $type = 'success';
            $message = 'Converted Successfully';

            // $rate = Swap::latest('USD/INR')->getValue();
            // using nepal rastriya bank api
            // $rate = 85.884499;
            $data = [
                'page' => 1,
                'per_page' => 10,
                'from' => date('Y-m-d'),
                'to' => date('Y-m-d')
            ];
            $currencyExchange = Http::get('https://www.nrb.org.np/api/forex/v1/rates/', $data);
            if ($currencyExchange->successful()) {
                $USDRateSell = $currencyExchange->json()['data']['payload'][0]['rates'][1]['sell'];
                // $USDRateBuy = $currencyExchange->json()['data']['payload'][0]['rates'][1]['buy'];
                // dd(floatval($USDRateSell) / 1.6, floatval($USDRateBuy) / 1.6, $rate);
                $rate = floatval($USDRateSell) / 1.6;
                $convertedAmount = $rate * intval($request->usd);
                // if ($request->paymentMode == "sbiBank") {
                $amount = ceil($convertedAmount);
                // } else {
                // $amount = ceil(0.0165 * $convertedAmount + $convertedAmount);
                // }
            } else {
                throw new Exception("Error Processing Request", 1);
            }
        } catch (Exception $e) {
            $type = 'error';
            $message = 'Error in QR Scan.';
            $amount = null;
        }
        return response()->json(['type' => $type, 'message' => $message, 'amount' => $amount]);
    }

    public function memberType(Request $request, $society, $conference)
    {
        try {
            $user = current_user();
            $country_id = $request->country_id;

            if (!$country_id) {
                return response()->json(['type' => 'error', 'message' => 'Country ID is required.', 'data' => []]);
            }

            if ($request->country_id == 125) {
                $types = MemberType::where([
                    'delegate' => 1,
                    'society_id' => $conference->society_id,
                    'status' => 1
                ])->get();
            } else {
                $types = MemberType::where([
                    'delegate' => 2,
                    'society_id' => $conference->society_id,
                    'status' => 1
                ])->get();
            }

            return response()->json([
                'type' => 'success',
                'message' => 'Member types fetched successfully.',
                'data' => $types
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Something went wrong.',
                'data' => []
            ]);
        }
    }
}
