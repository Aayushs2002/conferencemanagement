<?php

namespace App\Http\Controllers\Backend\Workshop\Workshop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Conference\WorkshopRequest;
use App\Models\User\Society;
use App\Models\Workshop\Workshop;
use App\Models\Workshop\WorkshopChairPersonDetail;
use App\Models\Workshop\WorkshopRegistrationPrice;
use App\Models\Workshop\WorkshopVenueDetail;
use App\Models\WorkshopRating;
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
        // Admin panel - shows all workshops for Type 1 & 2 (admins)
        $workshops = Workshop::where(['conference_id' => $conference->id, 'status' => 1])
            ->orderBy('display_order', 'asc')
            ->get();

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
            DB::beginTransaction();

            // Handle image upload
            if (!empty($validated['image'])) {
                $validated['image'] = $this->file_service->fileUpload($validated['image'], 'workshop-image', 'workshop/workshop/image/');
            }

            // Handle schedule/plan attachment upload
            // Only handle schedule_plan_attachment for type 3 users (normal users)
            if (current_user()->type == 3 && !empty($validated['schedule_plan_attachment'])) {
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
                'created_by' => auth()->id(),
                'is_published'=>true,
                // Type 1 & 2 (admins) auto-approve, Type 3 (normal users) need approval
                'approval_status' => (current_user()->type == 1 || current_user()->type == 2) ? 'approved' : 'pending',
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

            if (current_user()->type == 1 || current_user()->type == 2) {
                return redirect()->route('workshop.index', [$society, $conference])->with('status', 'Workshop Added Successfully');
            } else {
                return redirect()->route('workshop.index', [$society, $conference])->with('status', 'Workshop application submitted successfully! It will be reviewed by the admin.');
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('delete', 'Internal Server Error: ' . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function view($society, $conference, Request $request)
    {
        $workshop = Workshop::where('id', $request->id)->first();
        return view('backend.workshop.workshop.view', compact('workshop'));
    }

    public function viewRating($society, $conference, Request $request)
    {
        $workshop = Workshop::with(['ratings.user'])->findOrFail($request->id);

        // Get all ratings for this workshop
        $ratings = WorkshopRating::where('workshop_id', $workshop->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics
        $totalRatings = $ratings->count();
        $averageRating = $totalRatings > 0 ? $ratings->avg('rating') : 0;
        $ratingsWithComments = $ratings->filter(function ($rating) {
            return !empty($rating->comment);
        })->count();

        // Rating distribution (count for each star rating)
        $ratingDistribution = [
            5 => $ratings->where('rating', 5)->count(),
            4 => $ratings->where('rating', 4)->count(),
            3 => $ratings->where('rating', 3)->count(),
            2 => $ratings->where('rating', 2)->count(),
            1 => $ratings->where('rating', 1)->count(),
        ];

        return view('backend.workshop.workshop.view-rating', compact(
            'workshop',
            'ratings',
            'totalRatings',
            'averageRating',
            'ratingsWithComments',
            'ratingDistribution'
        ));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($society, $conference, Workshop $workshop)
    {
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
        // dd($request->all());
        try {
            $validated = $request->validated();
            $validated['conference_id'] = $conference->id;
            $validated['slug'] = slugify($validated['workshop_title']);
            DB::beginTransaction();

            // Handle image upload
            if (!empty($validated['image'])) {
                // dd('a');
                $this->file_service->deleteFile($workshop->image, 'workshop/workshop/image');
                $validated['image'] = $this->file_service->fileUpload($validated['image'], 'workshop-image', 'workshop/workshop/image/');
            }

            // Handle schedule plan attachment upload (only for type 3 users)
            if (current_user()->type == 3 && !empty($validated['schedule_plan_attachment'])) {
                if ($workshop->schedule_plan_attachment) {
                    $this->file_service->deleteFile($workshop->schedule_plan_attachment, 'workshop/schedules');
                }
                $validated['schedule_plan_attachment'] = $this->file_service->fileUpload($validated['schedule_plan_attachment'], 'schedule-plan', 'workshop/schedules/');
            }

            // If normal user is editing a correction_needed workshop, set status back to pending
            if (current_user()->type == 3 && $workshop->approval_status === 'correction_needed') {
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
            return redirect()->route('workshop.index', [$society, $conference])->with('status', 'Workshop Updated Successfully');
        } catch (\Throwable $th) {
            // dd($th->getMessage());
            DB::rollBack();
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

    /**
     * Approve workshop (for admin)
     */
    public function approve(Request $request, $society, $conference, $workshop)
    {
        $workshop = Workshop::where('id', $workshop)->first();
        try {
            $workshop->update([
                'approval_status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'admin_remarks' => $request->remarks
            ]);

            return redirect()->back()->with('status', 'Workshop approved successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('delete', 'Failed to approve workshop: ' . $e->getMessage());
        }
    }

    /**
     * Reject workshop (for admin)
     */
    public function reject(Request $request, $society, $conference, $workshop)
    {
        // dd($workshop);
        $workshop = Workshop::where('id', $workshop)->first();
        try {
            $request->validate([
                'remarks' => 'required|string'
            ]);

            $workshop->update([
                'approval_status' => 'rejected',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'admin_remarks' => $request->remarks
            ]);

            return redirect()->back()->with('status', 'Workshop rejected successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('delete', 'Failed to reject workshop: ' . $e->getMessage());
        }
    }

    /**
     * Request correction (for admin)
     */
    public function requestCorrection(Request $request, $society, $conference,  $workshop)
    {
        $workshop = Workshop::where('id', $workshop)->first();

        try {
            $request->validate([
                'remarks' => 'required|string'
            ]);

            $workshop->update([
                'approval_status' => 'correction_needed',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'admin_remarks' => $request->remarks
            ]);

            return redirect()->back()->with('status', 'Correction request sent successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('delete', 'Failed to send correction request: ' . $e->getMessage());
        }
    }

    /**
     * Update display order of workshops.
     */
    public function updateOrder(Request $request, $society, $conference)
    {
        try {
            $orders = $request->orders;
            
            foreach ($orders as $order) {
                Workshop::where('id', $order['id'])->update([
                    'display_order' => $order['position']
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle publish/unpublish status of workshop.
     */
    public function togglePublish(Request $request, $society, $conference, Workshop $workshop)
    {
        try {
            $isPublished = $request->is_published ? true : false;
            
            $workshop->update([
                'is_published' => $isPublished
            ]);

            $message = $isPublished 
                ? 'Workshop published successfully!' 
                : 'Workshop unpublished successfully!';

            return response()->json([
                'success' => true,
                'message' => $message,
                'is_published' => $isPublished
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update publish status: ' . $e->getMessage()
            ], 500);
        }
    }
}
