<?php

namespace App\Http\Controllers\Backend\Participant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Conference\WorkshopRequest;
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
    public function __construct(protected FileService $file_service) {}

    /** 
     * Display a listing of user's workshop applications
     */
    public function index($society, $conference)
    {
        // Normal users see only their own workshops
        $workshops = Workshop::where([
            'conference_id' => $conference->id,
            'status' => 1,
            'created_by' => current_user()->id
        ])->orderBy('created_at', 'desc')->get();

        return view('backend.participant.workshop.index', compact('society', 'conference', 'workshops'));
    }

    /**
     * Show the form for creating a new workshop application
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

        return view('backend.participant.workshop.create', compact('users', 'society', 'conference'));
    }

    /**
     * Store a newly created workshop application
     */
    public function store(WorkshopRequest $request, $society, $conference)
    {
        try {
            $validated = $request->validated();
            DB::beginTransaction();

            // Handle image upload
            if (!empty($validated['image'])) {
                $validated['image'] = $this->file_service->fileUpload($validated['image'], 'workshop-image', 'workshop/workshop/image/');
            }

            // Handle schedule plan attachment upload (REQUIRED for normal users)
            if (!empty($validated['schedule_plan_attachment'])) {
                $validated['schedule_plan_attachment'] = $this->file_service->fileUpload(
                    $validated['schedule_plan_attachment'],
                    'workshop-schedule-plan',
                    'workshop/schedules/'
                );
            }

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
                'slug' => slugify($validated['workshop_title']),
                'image' => $validated['image'] ?? null,
                'schedule_plan_attachment' => $validated['schedule_plan_attachment'] ?? null,
                'created_by' => current_user()->id,
                'approval_status' => 'pending', // Normal users always start with pending
                'proposed_budget' => $validated['proposed_budget'] ?? null,
                'registration_fee' => $validated['registration_fee'] ?? null,
                'overview_of_organiztion' => $validated['overview_of_organiztion'],
                'training_method_expected_outcome' => $validated['training_method_expected_outcome'],
                'resource_requirement' => $validated['resource_requirement'],
            ];

            $workshop = Workshop::create($workshopData);

            if (!empty($validated['photo'])) {
                $validated['photo'] = $this->file_service->fileUpload($validated['photo'], 'workshopchair_photo', 'workshop/chairperson/photo');
            }
            $validated['workshop_id'] = $workshop->id;

            WorkshopVenueDetail::create($validated);
            WorkshopChairPersonDetail::create($validated);

            DB::commit();

            return redirect()->route('my-society.conference.my-workshop.index', [$society, $conference])
                ->with('status', 'Workshop application submitted successfully! It will be reviewed by the admin.');
        } catch (Exception $e) {
            // dd($e->getMessage());
            DB::rollBack();
            return redirect()->back()->withInput()->with('delete', 'Error submitting workshop application: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified workshop
     */
    public function edit($society, $conference, Workshop $workshop)
    {
        // Ensure user can only edit their own workshops
        if ($workshop->created_by !== current_user()->id) {
            return redirect()->route('my-society.conference.my-workshop.index', [$society, $conference])
                ->with('delete', 'You can only edit your own workshops.');
        }

        // Can only edit if pending or correction_needed
        if (!in_array($workshop->approval_status, ['pending', 'correction_needed'])) {
            return redirect()->route('my-society.conference.my-workshop.index', [$society, $conference])
                ->with('delete', 'You can only edit workshops that are pending or need correction.');
        }

        $society = Society::with(['users' => function ($query) {
            $query->where('type', 3)->orderByDesc('id');
        }])->where([
            'id' => $conference->society_id,
            'status' => 1
        ])->first();

        $users = $society ? $society->users : collect();

        return view('backend.participant.workshop.create', compact('users', 'workshop', 'society', 'conference'));
    }

    /**
     * Update the specified workshop
     */
    public function update(WorkshopRequest $request, $society, $conference, Workshop $workshop)
    {
        try {
            // Ensure user can only update their own workshops
            if ($workshop->created_by !== current_user()->id) {
                return redirect()->route('my-society.conference.my-workshop.index', [$society, $conference])
                    ->with('delete', 'You can only edit your own workshops.');
            }

            $validated = $request->validated();
            $validated['conference_id'] = $conference->id;
            $validated['slug'] = slugify($validated['workshop_title']);
            // Ensure these fields are present for update
            $validated['proposed_budget'] = $validated['proposed_budget'] ?? null;
            $validated['registration_fee'] = $validated['registration_fee'] ?? null;

            DB::beginTransaction();

            // Handle image upload
            if (!empty($validated['image'])) {
                $this->file_service->deleteFile($workshop->image, 'workshop/workshop/image');
                $validated['image'] = $this->file_service->fileUpload($validated['image'], 'workshop-image', 'workshop/workshop/image/');
            }

            // Handle schedule plan attachment upload
            if (!empty($validated['schedule_plan_attachment'])) {
                if ($workshop->schedule_plan_attachment) {
                    $this->file_service->deleteFile($workshop->schedule_plan_attachment, 'workshop/schedules');
                }
                $validated['schedule_plan_attachment'] = $this->file_service->fileUpload($validated['schedule_plan_attachment'], 'schedule-plan', 'workshop/schedules/');
            }

            // If editing a correction_needed workshop, set status back to pending
            if ($workshop->approval_status === 'correction_needed') {
                $validated['approval_status'] = 'pending';
                $validated['admin_remarks'] = null;
            }

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

            return redirect()->route('my-society.conference.my-workshop.index', [$society, $conference])
                ->with('status', 'Workshop updated and resubmitted for review.');
        } catch (Exception $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('delete', 'Error updating workshop: ' . $th->getMessage());
        }
    }

    /**
     * View workshop details
     */
    public function view($society, $conference, Request $request)
    {
        $workshop = Workshop::where('id', $request->id)->where('created_by', current_user()->id)->first();

        if (!$workshop) {
            return response()->json(['error' => 'Workshop not found or access denied.'], 404);
        }

        return view('backend.participant.workshop.view', compact('workshop'));
    }

    /**
     * Remove the specified workshop (soft delete)
     */
    public function destroy($society, $conference, Workshop $workshop)
    {
        try {
            // Ensure user can only delete their own workshops
            if ($workshop->created_by !== current_user()->id) {
                return redirect()->route('my-society.conference.my-workshop.index', [$society, $conference])
                    ->with('delete', 'You can only delete your own workshops.');
            }

            // Can only delete if pending or rejected
            if (!in_array($workshop->approval_status, ['pending', 'rejected'])) {
                return redirect()->route('my-society.conference.my-workshop.index', [$society, $conference])
                    ->with('delete', 'You cannot delete approved workshops.');
            }

            $workshop->update(['status' => 0]);

            return redirect()->route('my-society.conference.my-workshop.index', [$society, $conference])
                ->with('status', 'Workshop application deleted successfully.');
        } catch (Exception $th) {
            return redirect()->back()->with('delete', 'Error deleting workshop.');
        }
    }

    /**
     * Show form to allocate registration prices
     */
    public function allocatePriceForm(Request $request, $society, $conference)
    {
        $workshop = Workshop::where('id', $request->id)
            ->where('created_by', current_user()->id)
            ->where('approval_status', 'approved')
            ->first();

        if (!$workshop) {
            return response()->json(['error' => 'Workshop not found or not approved.'], 404);
        }

        $workshop = Workshop::whereId($request->id)->first();

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
        // dd($memberTypes);
        return view('backend.participant.workshop.price-form', compact('workshop', 'memberTypes', 'society', 'conference'));
    }

    /**
     * Submit registration prices
     */
    public function allocatePriceSubmit(Request $request, $society, $conference)
    {
        try {
            $workshop = Workshop::where('id', $request->workshop_id)
                ->where('created_by', current_user()->id)
                ->where('approval_status', 'approved')
                ->first();

            if (!$workshop) {
                return response()->json(['type' => 'error', 'message' => 'Unauthorized access.']);
            }

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
