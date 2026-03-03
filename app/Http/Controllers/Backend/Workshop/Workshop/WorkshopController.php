<?php

namespace App\Http\Controllers\Backend\Workshop\Workshop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Conference\WorkshopRequest;
use App\Jobs\SendWorkshopBulkMailJob;
use App\Exports\WorkshopRegistrationExport;
use App\Exports\WorkshopTrainerExport;
use App\Models\User\Society;
use App\Models\Workshop\Workshop;
use App\Models\Workshop\WorkshopChairPersonDetail;
use App\Models\Workshop\WorkshopRegistration;
use App\Models\Workshop\WorkshopRegistrationPrice;
use App\Models\Workshop\WorkshopTrainer;
use App\Models\Workshop\WorkshopVenueDetail;
use App\Models\WorkshopRating;
use App\Services\File\FileService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel; 
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
                'workshop_slogan' => $validated['workshop_slogan'] ?? null,
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

    public function sendMail($society, $conference)
    {
        $workshops = Workshop::where(['conference_id' => $conference->id, 'status' => 1])
            ->orderBy('workshop_title', 'asc')
            ->get();

        return view('backend.workshop.workshop.send-mail-page', compact('workshops', 'society', 'conference'));
    }

    public function sendMailSubmit(Request $request, $society, $conference)
    {
        try {
            $type = 'success';
            $message = 'Mail sent successfully.';

            $validated = $request->validate([
                'workshop_id' => 'required|exists:workshops,id',
                'recipient_type' => 'required|in:1,2,3',
                'User' => 'required',
                'subject' => 'required',
                'mail_content' => 'required',
            ]);

            $users = json_decode($validated['User']);

            if (empty($users)) {
                throw new Exception('No recipients selected');
            }

            // Get workshop details
            $workshop = Workshop::with(['WorkshopVenueDetail', 'conference'])->findOrFail($validated['workshop_id']);

            $queuedCount = 0;
            foreach ($users as $userObj) {
                // Get full user with registration details
                $workshopRegistration = WorkshopRegistration::where('workshop_id', $workshop->id)
                    ->where('user_id', $userObj->value)
                    ->with(['user.userDetail.namePrefix', 'workshop'])
                    ->first();

                if (!$workshopRegistration || !$workshopRegistration->user) {
                    continue;
                }

                // Replace placeholders in message
                $messageContent = $this->replacePlaceholders(
                    $validated['mail_content'],
                    $workshopRegistration,
                    $workshop,
                    $society
                );

                $data = [
                    'name' => $workshopRegistration->user->fullName($workshopRegistration->user),
                    'namePrefix' => $workshopRegistration->user->userDetail->namePrefix->prefix ?? '',
                    'registrant_type' => $workshopRegistration->registrant_type,
                    'workshop_title' => $workshop->workshop_title,
                    'conference_name' => $conference->conference_name,
                ];

                SendWorkshopBulkMailJob::dispatch(
                    $workshopRegistration->user,
                    $validated['subject'],
                    $messageContent,
                    $data,
                    $conference->conference_name
                )->delay(now()->addSeconds($queuedCount * 3));

                $queuedCount++;
            }

            $message = "Email queued successfully for {$queuedCount} recipient(s).";
        } catch (Exception $e) {
            $type = 'error';
            $message = $e->getMessage();
        }

        return response()->json(['type' => $type, 'message' => $message]);
    }

    public function getUsersByWorkshopAndType(Request $request, $society, $conference)
    {
        try {
            $workshopId = $request->input('workshop_id');
            $recipientType = $request->input('recipient_type');

            if (!$workshopId || !$recipientType) {
                return response()->json([]);
            }

            $users = collect();

            // Recipient Type: 1 = Registrants, 2 = Trainers, 3 = Both
            if (in_array($recipientType, ['1', '3'])) {
                // Get registrants (verified and active only)
                $registrants = WorkshopRegistration::where('workshop_id', $workshopId)
                    ->where('verified_status', 1)
                    ->where('status', 1)
                    ->where('registrant_type', 1)
                    ->with('user:id,f_name,m_name,l_name,email')
                    ->get()
                    ->pluck('user')
                    ->filter();

                $users = $users->merge($registrants);
            }

            if (in_array($recipientType, ['2', '3'])) {
                // Get trainers (registrant_type = 2)
                $trainers = WorkshopRegistration::where('workshop_id', $workshopId)
                    ->where('verified_status', 1)
                    ->where('status', 1)
                    ->where('registrant_type', 2)
                    ->with('user:id,f_name,m_name,l_name,email')
                    ->get()
                    ->pluck('user')
                    ->filter();

                $users = $users->merge($trainers);
            }

            // Remove duplicates based on email
            $users = $users->unique('email')
                ->filter(function ($user) {
                    return !empty($user) && !empty($user->email);
                })
                ->map(function ($user) {
                    return [
                        'value' => $user->id,
                        'name' => trim("{$user->f_name} {$user->m_name} {$user->l_name}"),
                        'email' => $user->email,
                        'avatar' => 'https://i.pravatar.cc/80?u='.urlencode($user->email),
                    ];
                })->values();

            return response()->json($users);
        } catch (Exception $e) {
            \Log::error('Workshop getUsersByWorkshopAndType error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function exportRegistrations(Request $request, $society, $conference)
    {
        try {
            $query = WorkshopRegistration::where('status', 1)
                ->where('registrant_type', 1)
                ->with(['user.userDetail.country', 'user.userDetail.institution', 'workshop']);

            // Filter by conference
            $query->whereHas('workshop', function ($q) use ($conference) {
                $q->where('conference_id', $conference->id);
            });

            // Filter by workshop if provided
            if ($request->filled('workshop_id')) {
                $query->where('workshop_id', $request->workshop_id);
            }

            // Filter by verified status if provided
            if ($request->filled('verified_status')) {
                $query->where('verified_status', $request->verified_status);
            }

            // Filter by meal type if provided
            if ($request->filled('meal_type')) {
                $query->where('meal_type', $request->meal_type);
            }

            $registrants = $query->get();

            $fileName = 'Workshop_Registrations_' . date('Y-m-d_H-i-s') . '.xlsx';

            return Excel::download(new WorkshopRegistrationExport($registrants), $fileName);
        } catch (Exception $e) {
            return redirect()->back()->with('delete', 'Export failed: ' . $e->getMessage());
        }
    }

    public function exportTrainers(Request $request, $society, $conference)
    {
        try {
            $query = WorkshopRegistration::where('status', 1)
                ->where('registrant_type', 2)
                ->with(['user.userDetail.country', 'user.userDetail.institution', 'workshop']);

            // Filter by conference
            $query->whereHas('workshop', function ($q) use ($conference) {
                $q->where('conference_id', $conference->id);
            });

            // Filter by workshop if provided
            if ($request->filled('workshop_id')) {
                $query->where('workshop_id', $request->workshop_id);
            }

            // Filter by verified status if provided
            if ($request->filled('verified_status')) {
                $query->where('verified_status', $request->verified_status);
            }

            $trainers = $query->get();

            $fileName = 'Workshop_Trainers_' . date('Y-m-d_H-i-s') . '.xlsx';

            return Excel::download(new WorkshopTrainerExport($trainers), $fileName);
        } catch (Exception $e) {
            return redirect()->back()->with('delete', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Replace placeholders in message with actual values for workshop
     */
    private function replacePlaceholders($message, $registration, $workshop, $society)
    {
        $registrantTypes = [
            1 => 'Attendee',
            2 => 'Faculty',
        ];

        $certificateUrl = route('workshop-certificate.generateCertificate', [
            $society,
            $workshop->conference,
            $workshop,
            $registration->id
        ]);

        $placeholders = [
            '{name}' => $registration->user->fullName($registration->user),
            '{first_name}' => $registration->user->f_name,
            '{middle_name}' => $registration->user->m_name ?? '',
            '{last_name}' => $registration->user->l_name,
            '{prefix}' => $registration->user->userDetail->namePrefix->prefix ?? '',
            '{email}' => $registration->user->email,
            '{registrant_type}' => $registrantTypes[$registration->registrant_type] ?? 'Participant',
            '{registration_id}' => $registration->id ?? 'N/A',
            '{workshop_title}' => $workshop->workshop_title,
            '{workshop_slogan}' => $workshop->workshop_slogan ?? '',
            '{workshop_start_date}' => $workshop->start_date ? \Carbon\Carbon::parse($workshop->start_date)->format('jS F, Y') : 'N/A',
            '{workshop_end_date}' => $workshop->end_date ? \Carbon\Carbon::parse($workshop->end_date)->format('jS F, Y') : 'N/A',
            '{workshop_start_time}' => $workshop->start_time ?? 'N/A',
            '{workshop_end_time}' => $workshop->end_time ?? 'N/A',
            '{venue}' => $workshop->WorkshopVenueDetail->venue_name ?? 'N/A',
            '{venue_address}' => $workshop->WorkshopVenueDetail->venue_address ?? 'N/A',
            '{conference_name}' => $workshop->conference->conference_name ?? 'N/A',
            '{conference_theme}' => $workshop->conference->conference_theme ?? '',
            '{conference_start_date}' => $workshop->conference->start_date ? \Carbon\Carbon::parse($workshop->conference->start_date)->format('jS F, Y') : 'N/A',
            '{conference_end_date}' => $workshop->conference->end_date ? \Carbon\Carbon::parse($workshop->conference->end_date)->format('jS F, Y') : 'N/A',
            '{certificate_link}' => $certificateUrl,
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $message);
    }
}

