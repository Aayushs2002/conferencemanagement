<?php

namespace App\Http\Controllers\Backend\Conference;

use App\Http\Controllers\Controller;
use App\Http\Requests\Conference\ConferenceRequest;
use App\Models\Conference\Attendance;
use App\Models\Conference\Conference;
use App\Models\Conference\ConferenceOrganizer;
use App\Models\Conference\ConferenceRegistration;
use App\Models\Conference\ConferenceVenueDetail;
use App\Models\Conference\Submission;
use App\Models\User;
use App\Models\User\Society;
use App\Models\Workshop\Workshop;
use App\Models\Workshop\WorkshopRegistration;
use App\Services\File\FileService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class ConferenceController extends Controller
{
    /**  
     * Display a listing of the resource.
     */
    public function __construct(protected FileService $file_service) {}

    public function index($society)
    {
        // if (!empty(session()->get('conferenceDetail'))) {
        //     session()->forget('conferenceDetail');
        // }
        // $societyDetail = society_detail();

        // if (is_super_admin() && empty($societyDetail)) {
        //     return redirect()->route('dashboard');
        // }

        // if (is_society_admin()) {
        //     $conferences = Conference::where([
        //         'society_id' => current_user()->societies->value('id'),
        //         'status' => 1
        //     ])->latest()->get();
        // } elseif (is_super_admin()) {
        //     $conferences = Conference::where([
        //         'society_id' => $societyDetail->id,
        //         'status' => 1
        //     ])->latest()->get();
        // } else {
        //     return redirect()->route('dashboard');
        // }
        $conferences = Conference::where([
            'society_id' => $society->id,
            'status' => 1
        ])->latest()->get();

        return view('backend.conference.index', compact('conferences', 'society'));

        // return view('backend.conference.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($society)
    {
        return view('backend.conference.create', compact('society'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ConferenceRequest $request, $society)
    {
        try {
            $req = $request->all();
            //current_user function is custom helper function
            $req['society_id'] = $society->id;

            //slugify function is custom helper function
            $req['slug'] = slugify($req['conference_name']);

            DB::beginTransaction();

            //uploading the conference logo
            if (!empty($req['conference_logo'])) {
                //file uplaod function parameter required file,name,location
                $req['conference_logo'] = $this->file_service->fileUpload($req['conference_logo'], 'conference_logo', 'conference/conference/logo');
            }

            //uploading the organizer logo
            if (!empty($req['organizer_logo'])) {
                //file uplaod function parameter required file,name,location
                $req['organizer_logo'] = $this->file_service->fileUpload($req['organizer_logo'], 'organizer_logo', 'conference/organizer/logo');
            }
            //inserting in conference table
            $Conference = Conference::create($req);

            $req['conference_id'] = $Conference->id;

            //inserting in conference organizer table
            ConferenceOrganizer::create($req);

            //inserting in conference venue details table
            ConferenceVenueDetail::create($req);

            $SuperUser = User::where('type', 1)->firstOrFail();

            $societyAdmin = Society::findOrFail($society->id)
                ->users()
                ->where('type', 2)
                ->firstOrFail();

            $permissionIds = Permission::pluck('id')->all(); 
            $conferenceId = $Conference->id;

            foreach ($permissionIds as $permissionId) {
                // SuperUser: only attach if not already attached for this conference
                $exists = $SuperUser->conferencePermissions()
                    ->wherePivot('permission_id', $permissionId)
                    ->wherePivot('conference_id', $conferenceId)
                    ->exists();

                if (! $exists) {
                    $SuperUser->conferencePermissions()->attach($permissionId, ['conference_id' => $conferenceId]);
                }

                // SocietyAdmin: same logic
                $exists = $societyAdmin->conferencePermissions()
                    ->wherePivot('permission_id', $permissionId)
                    ->wherePivot('conference_id', $conferenceId)
                    ->exists();

                if (! $exists) {
                    $societyAdmin->conferencePermissions()->attach($permissionId, ['conference_id' => $conferenceId]);
                }
            }


            DB::commit();
            return redirect()->route('conference.index', $society)->with('status', 'Conference Added Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    /**
     * Display the specified resource.
     */
    public function view(Request $request)
    {
        $conference = Conference::whereId($request->id)->first();
        return view('backend.conference.view', compact('conference'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($society, Conference $conference)
    {
        return view('backend.conference.create', compact('conference', 'society'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ConferenceRequest $request, $society, Conference $conference)
    {
        try {
            $req = $request->all();
            //slugify function is custom helper function
            $req['slug'] = slugify($req['conference_name']);

            DB::beginTransaction();

            //uploading the conference logo
            if (!empty($req['conference_logo'])) {
                $this->file_service->deleteFile($conference->conference_logo, 'conference/conference/logo');
                //file uplaod function parameter required file,name,location
                $req['conference_logo'] = $this->file_service->fileUpload($req['conference_logo'], 'conference_logo', 'conference/conference/logo');
            }

            //uploading the organizer logo
            if (!empty($req['organizer_logo'])) {
                //deleting the file deleteFile function parameter required file,location
                $this->file_service->deleteFile($conference->organizer_logo, 'conference/organizer/logo');
                //file uplaod function parameter required file,name,location
                $req['organizer_logo'] = $this->file_service->fileUpload($req['organizer_logo'], 'organizer_logo', 'conference/organizer/logo');
            }
            //updating in conference table
            $conference->update($req);

            //conferenceOrganization table find and updated
            $conferenceOrganization = ConferenceOrganizer::whereConferenceId($conference->id)->first();
            if ($conferenceOrganization) {
                $conferenceOrganization->update($req);
            }

            //conferenceVenueDetail table find and updated
            $conferenceVenueDetail = ConferenceVenueDetail::whereConferenceId($conference->id)->first();
            if ($conferenceVenueDetail) {
                $conferenceVenueDetail->update($req);
            }
            DB::commit();
            return redirect()->route('conference.index', $society)->with('status', 'Conference Updated Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    public function openConferencePortal($society, $conference)
    {
        $conferenceRegistrationCount = ConferenceRegistration::where(['conference_id' => $conference->id, 'status' => 1])->count();
        $totalNationalRegistrants = ConferenceRegistration::totalRegistrants(1, $society, $conference);
        $totalInternationalRegistrants = ConferenceRegistration::totalRegistrants(2, $society, $conference);

        $mealCounts = DB::table('conference_registrations')
            ->select(
                DB::raw("CASE 
                    WHEN meal_type = 1 THEN 'Veg' 
                    WHEN meal_type = 2 THEN 'Non-Veg' 
                    ELSE 'Unknown' 
                 END as meal_label"),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('meal_type')
            ->where('conference_id', $conference->id)
            ->get();

        $conferenceId = $conference->id;


        $data = DB::table('conference_registrations')
            ->leftJoin('attendances', 'conference_registrations.id', '=', 'attendances.conference_registration_id')
            ->leftJoin('meals', 'conference_registrations.id', '=', 'meals.conference_registration_id')
            ->select(
                DB::raw('COUNT(DISTINCT attendances.id) as attendance_count'),
                DB::raw('COALESCE(SUM(meals.lunch_taken), 0) as lunch_count'),
                DB::raw('COALESCE(SUM(meals.dinner_taken), 0) as dinner_count')
            )
            ->where('conference_registrations.conference_id', $conferenceId)
            ->first();

        $startDate = Carbon::parse($conference->start_date);
        $endDate = Carbon::parse($conference->end_date);

        $dates = [];
        while ($startDate->lte($endDate)) {
            $dates[] = $startDate->toDateString();
            $startDate->addDay();
        }
        $workshops = Workshop::where(['conference_id' => $conference->id, 'status' => 1])->get();
        $workshopMealCounts = DB::table('workshop_registrations')
            ->select('workshop_id', 'meal_type', DB::raw('count(*) as count'))
            ->groupBy('workshop_id', 'meal_type')
            ->get()
            ->groupBy('workshop_id');
        $submissionCount = Submission::where(['conference_id' => $conference->id, 'user_id' => current_user()->id, 'status' => 1])->count();
        $workshop = Workshop::where(['conference_id' => $conference->id, 'status' => 1])->pluck('id');
        $workshopRegistrationCount = WorkshopRegistration::where(['user_id' => current_user()->id, 'status' => 1])->whereIn('workshop_id', $workshop)->count();
        return view('backend.conference.dashboard', compact('conferenceRegistrationCount', 'totalNationalRegistrants', 'totalInternationalRegistrants', 'mealCounts', 'conference', 'society', 'data', 'dates', 'workshops', 'workshopMealCounts', 'submissionCount', 'workshopRegistrationCount'));
    }

    public function viewAttendanceStatus($society, $conference)
    {
        $registrants = DB::table('conference_registrations as CR')
            ->select(
                'CR.id',
                'CR.status',
                'CR.conference_id',
                'CR.total_attendee',
                'CR.verified_status',
                'U.f_name',
                'U.m_name',
                'U.l_name'
            )
            ->where([
                'CR.verified_status' => 1,
                'CR.status' => 1,
                'CR.conference_id' => $conference->id,
            ])
            ->join('users as U', 'CR.user_id', '=', 'U.id')
            ->join('user_details as UD', 'U.id', '=', 'UD.user_id')
            ->orderBy('U.f_name', 'asc')
            ->get();

        // Attach meals
        foreach ($registrants as $registrant) {
            $meals = DB::table('meals')
                ->where('conference_registration_id', $registrant->id)
                ->select('lunch_taken', 'dinner_taken')
                ->get();

            $attendences = DB::table('attendances')
                ->where('conference_registration_id', $registrant->id)
                ->get();

            $registrant->meals = $meals;
            $registrant->attendences = $attendences;
        }
        return view('backend.conference.attendance-status', compact('registrants'));
    }
    public function getStats(Request $request)
    {
        $conferenceId = $request->conference_id;
        $date = $request->date;

        $query = DB::table('conference_registrations')
            ->leftJoin('attendances', 'conference_registrations.id', '=', 'attendances.conference_registration_id')
            ->leftJoin('meals', 'conference_registrations.id', '=', 'meals.conference_registration_id')
            ->where('conference_registrations.conference_id', $conferenceId);

        // Filter by date if not 'all'
        if ($date && $date !== 'all') {
            $query->whereDate('attendances.created_at', $date)
                ->whereDate('meals.created_at', $date);
        }

        $data = $query->select(
            DB::raw('COUNT(DISTINCT attendances.id) as attendance_count'),
            DB::raw('COALESCE(SUM(meals.lunch_taken), 0) as lunch_count'),
            DB::raw('COALESCE(SUM(meals.dinner_taken), 0) as dinner_count')
        )->first();

        // Return zeros if null
        return response()->json([
            'attendance_count' => $data->attendance_count ?? 0,
            'lunch_count' => $data->lunch_count ?? 0,
            'dinner_count' => $data->dinner_count ?? 0,
        ]);
    }
}
