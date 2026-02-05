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

        $passNameTags = ConferenceMemberTypeNameTag::where('conference_id', $conference->id)->get();
        $committeePassDesignations = ConferenceCommitteePassDesignation::where('conference_id', $conference->id)->get();
        
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
                'member_type_id' => 'required|array',
                'member_type_id.*' => 'required|integer',
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

            $arrayData = [];
            foreach ($request->member_type_id as $index => $memberTypeId) {
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
            ConferenceMemberTypeNameTag::insert($arrayData);


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

        $passNameTags = ConferenceMemberTypeNameTag::where('conference_id', $conference->id)->get();
        $committeePassDesignations = ConferenceCommitteePassDesignation::where('conference_id', $conference->id)->get();
        
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
                'member_ids' => 'array',
                'member_type_id.*' => 'required|integer',
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


            $dataToInsert = [];
            $dataToUpdate = [];

            foreach ($request->member_type_id as $index => $memberTypeId) {
                $row = [
                    'conference_id'   => $conference->id,
                    'member_type_id'  => $memberTypeId,
                    'registrant_type' => $request->registrant_type[$index],
                    'name_tag'        => $request->name_tag[$index],
                    'color'           => $request->color[$index] ?? null,
                    'updated_at'      => now(),
                ];

                $id = $request->member_ids[$index] ?? null;

                if ($id) {
                    $row['id'] = $id;
                    $dataToUpdate[] = $row;
                } else {
                    $row['created_at'] = now();
                    $dataToInsert[] = $row;
                }
            }

            if (!empty($dataToUpdate)) {
                foreach ($dataToUpdate as $row) {
                    \DB::table('conference_member_type_name_tags')
                        ->where('id', $row['id'])
                        ->update([
                            'member_type_id'  => $row['member_type_id'],
                            'registrant_type' => $row['registrant_type'],
                            'name_tag'        => $row['name_tag'],
                            'color'           => $row['color'],
                            'updated_at'      => $row['updated_at'],
                        ]);
                }
            }

            if (!empty($dataToInsert)) {
                \DB::table('conference_member_type_name_tags')->insert($dataToInsert);
            }

            // Handle committee pass designations
            if ($request->has('committee_id') && !empty($request->committee_id)) {
                $committeeDataToInsert = [];
                $committeeDataToUpdate = [];

                foreach ($request->committee_id as $index => $committeeId) {
                    $row = [
                        'conference_id'   => $conference->id,
                        'committee_id'  => $committeeId,
                        'designation_id' => $request->committee_designation_id[$index],
                        'name_tag'        => $request->committee_name_tag[$index],
                        'color'           => $request->committee_color[$index] ?? null,
                        'updated_at'      => now(),
                    ];

                    $id = $request->committee_ids[$index] ?? null;

                    if ($id) {
                        $row['id'] = $id;
                        $committeeDataToUpdate[] = $row;
                    } else {
                        $row['created_at'] = now();
                        $committeeDataToInsert[] = $row;
                    }
                }

                if (!empty($committeeDataToUpdate)) {
                    foreach ($committeeDataToUpdate as $row) {
                        \DB::table('conference_committee_pass_designations')
                            ->where('id', $row['id'])
                            ->update([
                                'committee_id'  => $row['committee_id'],
                                'designation_id' => $row['designation_id'],
                                'name_tag'        => $row['name_tag'],
                                'color'           => $row['color'],
                                'updated_at'      => $row['updated_at'],
                            ]);
                    }
                }

                if (!empty($committeeDataToInsert)) {
                    \DB::table('conference_committee_pass_designations')->insert($committeeDataToInsert);
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
