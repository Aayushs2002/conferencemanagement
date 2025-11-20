<?php

namespace App\Http\Controllers\Backend\Society;

use App\Http\Controllers\Controller;
use App\Models\Society\SocietySetting;
use App\Models\User\Society;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class SocietySettingController extends Controller
{
    public function societySetting(Request $request)
    {
        // Handle both numeric ID and hashid
        $societyId = is_numeric($request->id) ? $request->id : Hashids::decode($request->id)[0] ?? null;
        $society = Society::where('id', $societyId)->first();
        $societySetting = SocietySetting::where('society_id', $society->id)->first();
        return view('backend.users.societies.society-setting', compact('society', 'societySetting'));
    }

    public function societySettingSubmit(Request $request)
    {
        try {
            $request->validate([
                'society_id' => 'required|exists:societies,id',
                'member_type_api' => 'nullable|string|max:255',
                'member_detail_api' => 'nullable|string|max:255',
                'banner_title' => 'nullable|string|max:255',
                'banner_subtitle' => 'nullable|string'
            ]);

            $type = 'success';

            $societySetting = SocietySetting::where('society_id', $request->society_id)->first();

            $data = [
                'society_id' => $request->society_id,
                'member_type_api' => $request->member_type_api,
                'member_detail_api' => $request->member_detail_api,
                'banner_title' => $request->banner_title,
                'banner_subtitle' => $request->banner_subtitle
            ];

            if ($societySetting) {
                $societySetting->update($data);
                $message = "Society Setting updated successfully";
            } else {
                SocietySetting::create($data);
                $message = "Society Setting created successfully";
            }
        } catch (\Exception $e) {
            $type = 'error';
            $message = $e->getMessage();
        }

        return response()->json(['type' => $type, 'message' => $message]);
    }
}
