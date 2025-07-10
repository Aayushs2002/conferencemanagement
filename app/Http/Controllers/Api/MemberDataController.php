<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conference\ConferenceMemberTypePrice;
use App\Models\User\Country;
use App\Models\User\MemberType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MemberDataController extends Controller
{
    public function getMemberData(): JsonResponse
    {

        $data = [
            'countries' => Country::all(),
            'member_types' => MemberType::where('status', 1)->get()
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function getMemberTypePrice(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'member_type_id' => 'required|exists:conference_member_type_prices,member_type_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $memberTypePrices = ConferenceMemberTypePrice::where('member_type_id', $request->member_type_id)->get();

        if ($memberTypePrices->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'No prices found for this member type',
                'data' => ['member_types' => []]
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'member_types' => $memberTypePrices
            ]
        ]);
    }
}
