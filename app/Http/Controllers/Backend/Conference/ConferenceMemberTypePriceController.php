<?php

namespace App\Http\Controllers\backend\Conference;

use App\Http\Controllers\Controller;
use App\Models\Conference\Conference;
use App\Models\Conference\ConferenceMemberTypePrice;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Batch; 

class ConferenceMemberTypePriceController extends Controller
{
    public function priceForm(Request $request)
    {
        $conference = Conference::where('id', $request->id)->first();
        $condition = "WHERE conference_id = " . $conference->id;
        $sql = "SELECT
                    MT.id,
                    MT.type,
                    MT.delegate,
                    MTP.price_id,
                    MTP.conference_id,
                    MTP.member_type_id,
                    MTP.early_bird_amount,
                    MTP.regular_amount,
                    MTP.on_site_amount,
                    MTP.guest_amount
                FROM member_types AS MT
                LEFT JOIN
                    (SELECT
                        id AS price_id,
                        conference_id,
                        member_type_id,
                        early_bird_amount,
                        regular_amount,
                        on_site_amount,
                        guest_amount
                    FROM
                        conference_member_type_prices
                        $condition
                    ) AS MTP ON MT.id = MTP.member_type_id
                    WHERE MT.society_id = " . $conference->society_id;

        $memberTypes = DB::select($sql);
        // dd($memberTypes);
        return view('backend.conference.price-form', compact('memberTypes', 'conference'));
    }

    public function priceSubmit(Request $request)
    {
        try {
            $type = 'success';

            $conference = Conference::where('id', $request->conference_id)->first();

            $insertArray = [];
            $updateArray = [];
            foreach ($request->member_type_id as $key => $value) {
                if (empty($request->price_id[$key])) {
                    $array['conference_id'] = $conference->id;
                    $array['member_type_id'] = $value;
                    $array['early_bird_amount'] = $request->early_bird_amount[$key];
                    $array['regular_amount'] = $request->regular_amount[$key];
                    $array['on_site_amount'] = $request->on_site_amount[$key];
                    $array['guest_amount'] = $request->guest_amount[$key];
                    $array['created_at'] = now();
                    $array['updated_at'] = now();
                    $insertArray[] = $array;
                } else {
                    $updatedDataArray['id'] = $request->price_id[$key];
                    $updatedDataArray['conference_id'] = $conference->id;
                    $updatedDataArray['member_type_id'] = $value;
                    $updatedDataArray['early_bird_amount'] = $request->early_bird_amount[$key];
                    $updatedDataArray['regular_amount'] = $request->regular_amount[$key];
                    $updatedDataArray['on_site_amount'] = $request->on_site_amount[$key];
                    $updatedDataArray['guest_amount'] = $request->guest_amount[$key];
                    $updatedDataArray['updated_at'] = now();
                    $updateArray[] = $updatedDataArray;
                }
            }
            if (!empty($insertArray)) {
                ConferenceMemberTypePrice::insert($insertArray);
            }

            if (!empty($updateArray)) {
                Batch::update(new ConferenceMemberTypePrice, $updateArray, 'id');
            }

            if (empty($updateArray)) {
                $message = "Price Submitted successfully";
            } else {
                $message = "Price Updated successfully";
            }
        } catch (Exception $e) {
            $type = 'error';
            $message = $e->getMessage();
        }
        return response()->json(['type' => $type, 'message' => $message]);
    }
}
