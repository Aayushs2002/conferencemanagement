<?php

namespace App\Http\Controllers\Backend\Conference;

use App\Http\Controllers\Controller;
use App\Models\Conference\Conference;
use App\Models\Conference\ConferenceAddon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConferenceAddonController extends Controller
{
    public function addon(Request $request)
    {
        $conference = Conference::where('id', $request->id)->first();
        $addOns = ConferenceAddon::where('conference_id', $conference->id)->get();
        return view('backend.conference.addon-form', compact('conference', 'addOns'));
    }

    public function addOnSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'conference_id' => 'required|exists:conferences,id',
            'addon_name' => 'required|array|min:1',
            'addon_name.*' => 'required|string|max:255',
            'addon_national_amount' => 'required|array|min:1',
            'addon_national_amount.*' => 'required|numeric|min:0',
            'addon_international_amount' => 'required|array|min:1',
            'addon_international_amount.*' => 'required|numeric|min:0',
            'addon_ids' => 'nullable|array',
            'addon_ids.*' => 'nullable|integer|exists:conference_addons,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $conferenceId = $validated['conference_id'];
        $submittedIds = array_filter($request->addon_ids ?? []);
        $savedIds = $submittedIds; // will merge new IDs into this

        foreach ($validated['addon_name'] as $index => $addonName) {
            $id = $request->addon_ids[$index] ?? null;

            if ($id) {
                // Update existing
                ConferenceAddon::where('id', $id)->update([
                    'conference_id' => $conferenceId,
                    'addon_name' => $addonName,
                    'addon_national_amount' => $validated['addon_national_amount'][$index],
                    'addon_international_amount' => $validated['addon_international_amount'][$index],
                    'updated_at' => now(),
                ]);
            } else {
                // Insert and capture ID
                $newAddon = ConferenceAddon::create([
                    'conference_id' => $conferenceId,
                    'addon_name' => $addonName,
                    'addon_national_amount' => $validated['addon_national_amount'][$index],
                    'addon_international_amount' => $validated['addon_international_amount'][$index],
                ]);
                $savedIds[] = $newAddon->id;
            }
        }

        // Delete only records NOT in final saved IDs
        ConferenceAddon::where('conference_id', $conferenceId)
            ->whereNotIn('id', $savedIds)
            ->delete();

        return response()->json([
            'type' => 'success',
            'message' => 'Conference add-ons saved successfully!',
        ]);
    }
}
