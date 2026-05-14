<?php

namespace App\Http\Controllers\Backend\Conference;

use App\Http\Controllers\Controller;
use App\Models\Conference\Conference;
use App\Models\Conference\ConferenceAddon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ConferenceAddonController extends Controller
{ 
    public function addon(Request $request)
    {
        $conference = Conference::where('id', $request->id)->first();
        
        // Get member types for this conference's society
        $sql = "SELECT id, type, delegate 
                FROM member_types 
                WHERE society_id = " . $conference->society_id;
        $memberTypes = DB::select($sql);
        
        // Get existing addons grouped by name
        $addOns = ConferenceAddon::where('conference_id', $conference->id)->get();
        $addonsByName = $addOns->groupBy('addon_name');
        
        return view('backend.conference.addon-form', compact('conference', 'memberTypes', 'addonsByName'));
    }

    public function addOnSubmit(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'conference_id' => 'required|exists:conferences,id',
            'addon_names' => 'nullable|array|min:1',
            'addon_names.*' => 'nullable|string|max:255',
            'member_type_ids' => 'nullable|array',
            'early_bird_amounts' => 'nullable|array',
            'regular_amounts' => 'nullable|array',
            'late_amounts' => 'nullable|array',
            'on_site_amounts' => 'nullable|array',
            'guest_amounts' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $conferenceId = $request->conference_id;
            $savedIds = [];

            // Process each addon name
            foreach ($request->addon_names as $index => $addonName) {
                $memberTypeIds = $request->member_type_ids[$addonName] ?? [];
                
                foreach ($memberTypeIds as $mtIndex => $memberTypeId) {
                    $addonId = $request->addon_ids[$addonName][$mtIndex] ?? null;
                    
                    $data = [
                        'conference_id' => $conferenceId,
                        'addon_name' => $addonName,
                        'member_type_id' => $memberTypeId,
                        'early_bird_amount' => $request->early_bird_amounts[$addonName][$mtIndex] ?? null,
                        'regular_amount' => $request->regular_amounts[$addonName][$mtIndex] ?? null,
                        'late_amount' => $request->late_amounts[$addonName][$mtIndex] ?? null,
                        'on_site_amount' => $request->on_site_amounts[$addonName][$mtIndex] ?? null,
                        'guest_amount' => $request->guest_amounts[$addonName][$mtIndex] ?? null,
                    ];
                    
                    if ($addonId) {
                        // Update existing
                        ConferenceAddon::where('id', $addonId)->update(array_merge($data, [
                            'updated_at' => now(),
                        ]));
                        $savedIds[] = $addonId;
                    } else {
                        // Create new
                        $newAddon = ConferenceAddon::create($data);
                        $savedIds[] = $newAddon->id;
                    }
                }
            }

            // Delete records not in the saved IDs list
            ConferenceAddon::where('conference_id', $conferenceId)
                ->whereNotIn('id', $savedIds)
                ->delete();

            return response()->json([
                'type' => 'success',
                'message' => 'Conference add-ons saved successfully!',
            ]);
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return response()->json([
                'type' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}

