<?php

namespace App\Http\Controllers\Backend\Workshop\Workshop;

use App\Http\Controllers\Controller;
use App\Http\Requests\conference\WorkshopRequest;
use App\Models\User\Society;
use App\Models\Workshop\Workshop;
use App\Models\Workshop\WorkshopChairPersonDetail;
use App\Models\Workshop\WorkshopRegistrationPrice;
use App\Models\Workshop\WorkshopVenueDetail;
use App\Services\File\FileService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Batch;

class WorkshopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(protected FileService $file_service) {}

    public function index($society, $conference)
    {
        // $conferenceDetail = conference_detail();
        // if (empty($conferenceDetail)) {
        //     return redirect()->route('dashboard');
        // }
        $workshops = Workshop::where(['conference_id' => $conference->id, 'status' => 1])->get();
        return view('backend.workshop.workshop.index', compact('workshops', 'society', 'conference'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($society, $conference)
    {


        $society = Society::with(['users' => function ($query) {
            $query->where('type', 3)->orderByDesc('id');
        }])->where([
            'id' => $conference->society_id,
            'status' => 1
        ])->first();

        $users = $society ? $society->users : collect();

        return view('backend.workshop.workshop.create', compact('users', 'society', 'conference'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WorkshopRequest $request, $society, $conference)
    {
        try {
            $validated = $request->validated();
            $validated['conference_id'] = $conference->id;
            // dd($validated);
            DB::beginTransaction();
            $workshopData = [
                'conference_id' => $conference->id,
                'workshop_title' => $validated['workshop_title'],
                'workshop_type' => $validated['workshop_type'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'registration_deadline' => $validated['registration_deadline'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'contact_person_name' => $validated['contact_person_name'],
                'contact_person_phone' => $validated['contact_person_phone'],
                'contact_person_email' => $validated['contact_person_email'],
                'no_of_participants' => $validated['no_of_participants'],
                'workshop_description' => $validated['workshop_description'],
                'slug' => slugify($validated['workshop_title'])
            ];

            $workshop = Workshop::create($workshopData);
            // dd($validated);

            if (!empty($validated['photo'])) {
                $validated['photo'] = $this->file_service->fileUpload($validated['photo'], 'workshopchair_photo', 'workshop/chairperson/photo');
            }
            $validated['workshop_id'] = $workshop->id;

            WorkshopVenueDetail::create($validated);

            WorkshopChairPersonDetail::create($validated);
            DB::commit();
            return redirect()->route('workshop.index', [$society, $conference])->with('status', 'Workshop Added Successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('delete', 'Internal Server Error');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($society, $conference, Workshop $workshop)
    {
        // $conferenceDetail = conference_detail();
        // if (empty($conferenceDetail)) {
        //     return redirect()->route('dashboard');
        // }

        $society = Society::with(['users' => function ($query) {
            $query->where('type', 3)->orderByDesc('id');
        }])->where([
            'id' => $conference->society_id,
            'status' => 1
        ])->first();

        $users = $society ? $society->users : collect();

        return view('backend.workshop.workshop.create', compact('users', 'workshop', 'society', 'conference'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WorkshopRequest $request, $society, $conference, Workshop $workshop)
    {
        try {
            $validated = $request->validated();
            $validated['conference_id'] = $conference->id;
            $validated['slug'] = slugify($validated['workshop_title']);
            DB::beginTransaction();

            $workshop->update($validated);

            $WorkshopVenueDetail = WorkshopVenueDetail::whereWorkshopId($workshop->id)->first();
            if ($WorkshopVenueDetail) {
                $WorkshopVenueDetail->update($validated);
            }

            $workshopChairPersonDetail = WorkshopChairPersonDetail::whereWorkshopId($workshop->id)->first();
            if ($workshopChairPersonDetail) {
                if (!empty($validated['photo'])) {
                    $this->file_service->deleteFile($workshopChairPersonDetail->photo, 'workshop/chairperson/photo');

                    $validated['photo'] = $this->file_service->fileUpload($validated['photo'], 'workshopchair_photo', 'workshop/chairperson/photo');
                }
                $workshopChairPersonDetail->update($validated);
            }
            DB::commit();
            return redirect()->route('workshop.index', [$society, $conference])->with('status', 'Workshop Updated Successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('delete', 'Internal Server Error');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($society, $conference, Workshop $workshop)
    {
        try {
            $workshop->update(['status' => 0]);
            return redirect()->route('workshop.index', [$society, $conference])->with('status', 'Workshop Deleted Successfully');
        } catch (Exception $th) {
            return redirect()->back()->withInput()->with('delete', 'Internal Server Error');
        }
    }

    public function allocatePriceForm(Request $request, $society, $conference)
    {
        $workshop = Workshop::whereId($request->id)->first();
        // $conferenceDetail = conference_detail();
        // if (empty($conferenceDetail)) {
        //     return redirect()->route('dashboard');
        // }

        $condition = "MT.society_id =" . $conference->society_id;

        $sql = "SELECT
                    MT.id,
                    MT.type,
                    MT.delegate,
                    WRP.price_id,
                    WRP.workshop_id,
                    WRP.member_type_id,
                    WRP.price,
                    WRP.discount_price
                FROM member_types AS MT
                LEFT JOIN
                    (SELECT
                        id AS price_id,
                        workshop_id,
                        member_type_id,
                        price,
                        discount_price
                    FROM
                        workshop_registration_prices
                        WHERE workshop_id = $workshop->id
                    ) AS WRP ON MT.id = WRP.member_type_id WHERE MT.status = 1 AND " . $condition;

        $memberTypes = DB::select($sql);
        return view('backend.workshop.workshop.price-form', compact('workshop', 'memberTypes', 'society', 'conference'));
    }

    public function allocatePriceSubmit(Request $request)
    {
        try {
            $type = 'success';
            $insertArray = [];
            $updateArray = [];
            foreach ($request->member_type_id as $key => $value) {
                if (empty($request->price_id[$key])) {
                    $array['workshop_id'] = $request->workshop_id;
                    $array['member_type_id'] = $value;
                    $array['price'] = $request->price[$key];
                    $array['discount_price'] = $request->discount_price[$key];
                    $array['created_at'] = now();
                    $array['updated_at'] = now();
                    $insertArray[] = $array;
                } else {
                    $updatedDataArray['id'] = $request->price_id[$key];
                    $updatedDataArray['workshop_id'] = $request->workshop_id;
                    $updatedDataArray['member_type_id'] = $value;
                    $updatedDataArray['price'] = $request->price[$key];
                    $updatedDataArray['discount_price'] = $request->discount_price[$key];
                    $updatedDataArray['updated_at'] = now();
                    $updateArray[] = $updatedDataArray;
                }
            }

            if (!empty($insertArray)) {
                WorkshopRegistrationPrice::insert($insertArray);
            }

            if (!empty($updateArray)) {
                Batch::update(new WorkshopRegistrationPrice, $updateArray, 'id');
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
