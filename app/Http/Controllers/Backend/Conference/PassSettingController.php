<?php

namespace App\Http\Controllers\Backend\Conference;

use App\Http\Controllers\Controller;
use App\Models\Conference\ConferenceRegistrationKit;
use App\Models\Conference\PassSetting;
use App\Models\ConferenceMemberTypeNameTag;
use App\Models\ConferenceCommitteePassDesignation;
use App\Models\Committee\Committee;
use App\Models\Committee\CommitteeDesignation;
use App\Models\User\MemberType;
use App\Services\File\FileService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PassSettingController extends Controller
{
    use AuthorizesRequests;
 
    /**
     * Display a listing of the resource. 
     */
    public function __construct(protected FileService $file_service) {}

    public function index($society, $conference)
    {
        $pass_setting = PassSetting::where(['conference_id' => $conference->id, 'status' => 1])->first();

        return view('backend.conference.conference-pass.index', compact('pass_setting', 'society', 'conference'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($society, $conference)
    {
        $memberTypes = MemberType::where('society_id', $conference->society_id)->get();
        $committees = Committee::where('society_id', $society->id)->where('status', 1)->get();
        $committeeDesignations = CommitteeDesignation::where('society_id', $society->id)->where('status', 1)->get();

        // Group pass name tags by registrant_type and name_tag to handle multiple member types
        $passNameTagsRaw = ConferenceMemberTypeNameTag::where('conference_id', $conference->id)->get();
        $passNameTags = $passNameTagsRaw->groupBy(function($item) {
            return $item->registrant_type . '_' . $item->name_tag . '_' . $item->color;
        })->map(function($group) {
            $first = $group->first();
            $first->member_type_id = $group->pluck('member_type_id')->toArray();
            return $first;
        })->values();

        // Group committee pass designations by designation_id and name_tag
        $committeePassDesignationsRaw = ConferenceCommitteePassDesignation::where('conference_id', $conference->id)->get();
        $committeePassDesignations = $committeePassDesignationsRaw->groupBy(function($item) {
            return $item->designation_id . '_' . $item->name_tag . '_' . $item->color;
        })->map(function($group) {
            $first = $group->first();
            $first->committee_id = $group->pluck('committee_id')->toArray();
            return $first;
        })->values();
        
        return view('backend.conference.conference-pass.create', compact('society', 'conference', 'memberTypes', 'passNameTags', 'committees', 'committeeDesignations', 'committeePassDesignations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $society, $conference)
    {
        try {
            $validated = $request->validate([
                'image' => 'required|mimes:png,jpg,',
                'lunch_start_time' => 'required|date_format:H:i',
                'lunch_end_time' => 'required|date_format:H:i',
                'dinner_start_time' => 'required|date_format:H:i',
                'dinner_end_time' => 'required|date_format:H:i',
                'workshop_participant_name_tag' => 'nullable|string|max:255',
                'workshop_participant_color' => 'nullable|string|max:7',
                'workshop_trainer_name_tag' => 'nullable|string|max:255',
                'workshop_trainer_color' => 'nullable|string|max:7',
                'member_type_id' => 'required|array',
                'member_type_id.*' => 'required',
                'registrant_type' => 'required|array',
                'name_tag' => 'required|array',
                'color' => 'nullable|array',
                'color.*' => 'nullable|string|max:7',
            ]);
            $validated['conference_id'] = $conference->id;

            if (!empty($validated['image'])) {
                //file uplaod function parameter required file,name,location
                $validated['image'] = $this->file_service->fileUpload($validated['image'], 'pass_image', 'conference/conference/pass');
            }
            // dd($validated);
            PassSetting::create($validated);

            // Handle multiple member types - create a row for each selected member type
            $arrayData = [];
            foreach ($request->member_type_id as $index => $memberTypeIds) {
                // Convert to array if it's a single value
                $memberTypeIdsArray = is_array($memberTypeIds) ? $memberTypeIds : [$memberTypeIds];
                
                foreach ($memberTypeIdsArray as $memberTypeId) {
                    $arrayData[] = [
                        'conference_id' => $conference->id,
                        'member_type_id' => $memberTypeId,
                        'registrant_type' => $request->registrant_type[$index],
                        'name_tag' => $request->name_tag[$index],
                        'color' => $request->color[$index] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            ConferenceMemberTypeNameTag::insert($arrayData);

            // Handle committee pass designations
            if ($request->has('committee_id') && !empty($request->committee_id)) {
                $committeeData = [];
                foreach ($request->committee_id as $index => $committeeIds) {
                    // Convert to array if it's a single value
                    $committeeIdsArray = is_array($committeeIds) ? $committeeIds : [$committeeIds];
                    
                    foreach ($committeeIdsArray as $committeeId) {
                        $committeeData[] = [
                            'conference_id' => $conference->id,
                            'committee_id' => $committeeId,
                            'designation_id' => $request->committee_designation_id[$index],
                            'name_tag' => $request->committee_name_tag[$index],
                            'color' => $request->committee_color[$index] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                ConferenceCommitteePassDesignation::insert($committeeData);
            }


            return redirect()->route('pass-setting.index', [$society, $conference])->with('status', 'Pass Setting Added Successfully');
        } catch (Exception $e) {
            // dd($e);
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($society, $conference, PassSetting $pass_setting)
    {
        $this->authorize('edit', $pass_setting);

        $memberTypes = MemberType::where('society_id', $conference->society_id)->get();
        $committees = Committee::where('society_id', $society->id)->where('status', 1)->get();
        $committeeDesignations = CommitteeDesignation::where('society_id', $society->id)->where('status', 1)->get();

        // Group pass name tags by registrant_type and name_tag to handle multiple member types
        $passNameTagsRaw = ConferenceMemberTypeNameTag::where('conference_id', $conference->id)->get();
        $passNameTags = $passNameTagsRaw->groupBy(function($item) {
            return $item->registrant_type . '_' . $item->name_tag . '_' . $item->color;
        })->map(function($group) {
            $first = $group->first();
            $first->member_type_id = $group->pluck('member_type_id')->toArray();
            return $first;
        })->values();

        // Group committee pass designations by designation_id and name_tag
        $committeePassDesignationsRaw = ConferenceCommitteePassDesignation::where('conference_id', $conference->id)->get();
        $committeePassDesignations = $committeePassDesignationsRaw->groupBy(function($item) {
            return $item->designation_id . '_' . $item->name_tag . '_' . $item->color;
        })->map(function($group) {
            $first = $group->first();
            $first->committee_id = $group->pluck('committee_id')->toArray();
            return $first;
        })->values();
        
        return view('backend.conference.conference-pass.create', compact(
            'society',
            'conference',
            'pass_setting',
            'memberTypes',
            'passNameTags',
            'committees',
            'committeeDesignations',
            'committeePassDesignations'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $society, $conference, PassSetting $pass_setting)
    {
        try {
            $validated = $request->validate([
                'image' => 'nullable|mimes:png,jpg,',
                'lunch_start_time' => 'required|date_format:H:i',
                'lunch_end_time' => 'required|date_format:H:i',
                'dinner_start_time' => 'required|date_format:H:i',
                'dinner_end_time' => 'required|date_format:H:i',
                'workshop_participant_name_tag' => 'nullable|string|max:255',
                'workshop_participant_color' => 'nullable|string|max:7',
                'workshop_trainer_name_tag' => 'nullable|string|max:255',
                'workshop_trainer_color' => 'nullable|string|max:7',
                'member_type_id' => 'required|array',
                'member_type_id.*' => 'required',
                'registrant_type' => 'required|array',
                'name_tag' => 'required|array',
                'color' => 'nullable|array',
                'color.*' => 'nullable|string|max:7',
            ]);
            if (!empty($validated['image'])) {
                //deleting the file deleteFile function parameter required file,location
                $this->file_service->deleteFile($pass_setting->image, 'conference/conference/pass');
                //file uplaod function parameter required file,name,location
                $validated['image'] = $this->file_service->fileUpload($validated['image'], 'pass_image', 'conference/conference/pass');
            }
            $pass_setting->update($validated);

            // Delete existing records for this conference
            ConferenceMemberTypeNameTag::where('conference_id', $conference->id)->delete();

            // Insert new records - handle multiple member types
            $dataToInsert = [];
            foreach ($request->member_type_id as $index => $memberTypeIds) {
                // Convert to array if it's a single value
                $memberTypeIdsArray = is_array($memberTypeIds) ? $memberTypeIds : [$memberTypeIds];
                
                foreach ($memberTypeIdsArray as $memberTypeId) {
                    $dataToInsert[] = [
                        'conference_id'   => $conference->id,
                        'member_type_id'  => $memberTypeId,
                        'registrant_type' => $request->registrant_type[$index],
                        'name_tag'        => $request->name_tag[$index],
                        'color'           => $request->color[$index] ?? null,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ];
                }
            }

            if (!empty($dataToInsert)) {
                ConferenceMemberTypeNameTag::insert($dataToInsert);
            }

            // Handle committee pass designations
            if ($request->has('committee_id') && !empty($request->committee_id)) {
                // Delete existing committee designations
                ConferenceCommitteePassDesignation::where('conference_id', $conference->id)->delete();

                $committeeDataToInsert = [];

                foreach ($request->committee_id as $index => $committeeIds) {
                    // Convert to array if it's a single value
                    $committeeIdsArray = is_array($committeeIds) ? $committeeIds : [$committeeIds];
                    
                    foreach ($committeeIdsArray as $committeeId) {
                        $committeeDataToInsert[] = [
                            'conference_id'   => $conference->id,
                            'committee_id'    => $committeeId,
                            'designation_id'  => $request->committee_designation_id[$index],
                            'name_tag'        => $request->committee_name_tag[$index],
                            'color'           => $request->committee_color[$index] ?? null,
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ];
                    }
                }

                if (!empty($committeeDataToInsert)) {
                    ConferenceCommitteePassDesignation::insert($committeeDataToInsert);
                }
            }

            return redirect()->route('pass-setting.index', [$society, $conference])->with('status', 'Pass Setting Updated Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
