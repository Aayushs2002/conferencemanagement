<?php

namespace App\Http\Controllers\Backend\Society;

use App\Http\Controllers\Controller;
use App\Http\Requests\Society\AddSocietyRequest;
use App\Models\Cms\Feature;
use App\Models\Conference\Conference;
use App\Models\Conference\ConferenceRegistration;
use App\Models\Conference\Submission;
use App\Models\Workshop\Workshop;
use App\Models\Workshop\WorkshopRegistration;
use App\Models\User;
use App\Models\User\MemberType;
use App\Models\User\Society;
use App\Models\User\UserSociety;
use App\Services\File\FileService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class SocietyController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(protected FileService $file_service) {}
    public function index()
    {
        if (is_super_admin()) {
            $societies  = Society::whereStatus(1)->get();
        } else {
            $societies = current_user()->societies;
        }
        // dd($societies);
        return view('backend.users.societies.index', compact('societies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $features = Feature::whereStatus(1)->get();
        return view('backend.users.societies.create', compact('features'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddSocietyRequest $request)
    {
        try { 
            $req = $request->all();
            $req['password'] = hash_password('password');
            $req['type'] = 2;
            $req['slug'] = slugify($req['society_name']);
            $req['f_name'] = $req['society_name'];
            $req['token'] = random_word(60);
            unset($req['society_name']);
            DB::beginTransaction();
            if (!empty($req['logo'])) {
                $req['logo'] = $this->file_service->fileUpload($req['logo'], 'societies', 'society/logo');
            }
            // insert into user detail table
            $society = Society::create($req);

            // insert into user table
            $user = User::create($req);

            //insert into pivot table user society
            // UserSociety::create([
            //     'user_id' => $user->id,
            //     'society_id' => $society->id
            // ]);
            $user->societies()->attach($society->id);

            //create role society admin role for this society
            $role = Role::where('name', 'society admin')->first();
            if (!$role) {
                $role = Role::create([
                    'name' => 'society admin',
                    'guard_name' => 'web',
                ]);
                $role->givePermissionTo(Permission::all());
                $user->assignRole($role);
            }
            if ($request->has('features')) {
                $society->features()->sync($request->features);
            }
            DB::commit();
            return redirect()->route('society.index')->with('status', 'Society Added Successfully');
        } catch (Exception $e) {
            // dd($e);
            DB::rollBack();
            return redirect()->back()->with('delete', 'Internal Server Error');
            // throw $e;
        }
    }


    public function view(Request $request)
    {
        $society = Society::whereId($request->id)->first();
        return view('backend.users.societies.view', compact('society'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Society $society)
    {
        $features = Feature::whereStatus(1)->get();

        $societyFeatures = $society->features->pluck('id')->toArray();
        return view('backend.users.societies.create', compact('society', 'features', 'societyFeatures'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AddSocietyRequest $request, Society $society)
    {
        // dd($request);
        try {
            $req = $request->all();
            $req['slug'] = slugify($req['society_name']);
            $req['f_name'] = $req['society_name'];
            unset($req['society_name']);
            DB::beginTransaction();
            // update society table
            if (!empty($req['logo'])) {
                $this->file_service->deleteFile($society->logo, 'society/logo');
                $req['logo'] = $this->file_service->fileUpload($req['logo'], 'societies', 'society/logo');
            }
            $society->update($req);

            // update user table
            $user = $society->users->where('type', 2)->first();
            $user->update($req);

            if ($request->has('features')) {
                $society->features()->sync($request->features);
            }
            DB::commit();
            return redirect()->route('society.index')->with('status', 'Society Updated Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    /** 
     * Remove the specified resource from storage.
     */
    public function destroy(Society $society)
    {
        try {
            $user = User::whereSocietyId($society->id)->first();

            $user->update(['status' => 0]);
            $society->update(['status' => 0]);

            return redirect()->back()->with('delete', 'Society Deleted Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    public function dashboard($society)
    {
        // Get all conferences for the society
        $allConferences = Conference::where([
            'society_id' => $society->id,
            'status' => 1
        ])->orderBy('start_date', 'desc')->get();

        // Get the latest conference as default
        $selectedConference = Conference::where([
            'society_id' => $society->id,
            'status' => 1,
            'is_archived' => 0
        ])->orderBy('start_date', 'desc')->first();

        // If no active conference, get the latest one
        if (!$selectedConference) {
            $selectedConference = Conference::where([
                'society_id' => $society->id,
                'status' => 1
            ])->orderBy('start_date', 'desc')->first();
        }

        // Basic Counts (Society-wide)
        $conferenceCount = Conference::where([
            'society_id' => $society->id,
            'status' => 1
        ])->count();
        
        $activeConferenceCount = Conference::where([
            'society_id' => $society->id,
            'status' => 1,
            'is_archived' => 0
        ])->count();
        
        $typeCount = MemberType::where([
            'society_id' => $society->id,
            'status' => 1
        ])->count();

        // Conference status counts
        $ongoingConferences = Conference::where('society_id', $society->id)
            ->where('status', 1)
            ->where('is_archived', 0)
            ->whereDate('start_date', '<=', Carbon::now())
            ->whereDate('end_date', '>=', Carbon::now())
            ->count();

        $completedConferences = Conference::where('society_id', $society->id)
            ->where('status', 1)
            ->whereDate('end_date', '<', Carbon::now())
            ->count();
        
        $upcomingConferences = Conference::where('society_id', $society->id)
            ->where('status', 1)
            ->where('is_archived', 0)
            ->where('start_date', '>=', Carbon::now())
            ->orderBy('start_date', 'asc')
            ->count();

        // Conference-specific statistics (for selected conference only)
        if ($selectedConference) {
            // Registration Statistics
            $totalRegistrations = ConferenceRegistration::where('conference_id', $selectedConference->id)
                ->where('verified_status', 1)
                ->count();
            
            $pendingRegistrations = ConferenceRegistration::where('conference_id', $selectedConference->id)
                ->where('verified_status', 0)
                ->count();

            // Submission Statistics
            $totalSubmissions = Submission::where('conference_id', $selectedConference->id)->count();
            
            $acceptedSubmissions = Submission::where('conference_id', $selectedConference->id)
                ->where('review_status', 1)
                ->count();

            $pendingSubmissions = Submission::where('conference_id', $selectedConference->id)
                ->where('review_status', 0)
                ->count();
            
            $rejectedSubmissions = Submission::where('conference_id', $selectedConference->id)
                ->where('review_status', 2)
                ->count();

            // Workshop Statistics
            $workshopIds = Workshop::where('conference_id', $selectedConference->id)->pluck('id');
            $workshopCount = $workshopIds->count();
            $workshopRegistrations = WorkshopRegistration::whereIn('workshop_id', $workshopIds)->count();

            // Recent Activity - Last 6 months registrations by month
            $sixMonthsAgo = Carbon::now()->subMonths(6);
            $monthlyRegistrations = ConferenceRegistration::where('conference_id', $selectedConference->id)
                ->where('created_at', '>=', $sixMonthsAgo)
                ->where('verified_status', 1)
                ->select(
                    DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month"),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        } else {
            // No conference selected - set defaults
            $totalRegistrations = 0;
            $pendingRegistrations = 0;
            $totalSubmissions = 0;
            $acceptedSubmissions = 0;
            $pendingSubmissions = 0;
            $rejectedSubmissions = 0;
            $workshopCount = 0;
            $workshopRegistrations = 0;
            $monthlyRegistrations = collect([]);
        }

        // Conference registration breakdown by conference (top 5)
        $conferenceRegistrationData = Conference::where('society_id', $society->id)
            ->where('status', 1)
            ->where('is_archived', 0)
            ->withCount(['conferenceRegistrations' => function($query) {
                $query->where('verified_status', 1);
            }])
            ->orderBy('conference_registrations_count', 'desc')
            ->limit(5)
            ->get();

        // Conferences with details
        $conferences = Conference::where([
            'society_id' => $society->id,
            'is_archived' => 0,
            'status' => 1
        ])->with(['ConferenceVenueDetail', 'ConferenceOrganizer'])
        ->withCount(['conferenceRegistrations' => function($query) {
            $query->where('verified_status', 1);
        }])
        ->withCount(['submissions'])
        ->orderBy('start_date', 'desc')
        ->limit(10)
        ->get();

        return view('backend.users.societies.dashboard', compact(
            'allConferences',
            'selectedConference',
            'conferenceCount',
            'activeConferenceCount',
            'typeCount',
            'totalRegistrations',
            'pendingRegistrations',
            'totalSubmissions',
            'acceptedSubmissions',
            'pendingSubmissions',
            'rejectedSubmissions',
            'workshopCount',
            'workshopRegistrations',
            'ongoingConferences',
            'completedConferences',
            'upcomingConferences',
            'monthlyRegistrations',
            'conferenceRegistrationData',
            'conferences',
            'society'
        ));
    }

    public function getDashboardData(Request $request, $society)
    {
        $conferenceId = $request->input('conference_id');
        
        if (!$conferenceId) {
            return response()->json(['error' => 'Conference ID is required'], 400);
        }

        $selectedConference = Conference::where('id', $conferenceId)
            ->where('society_id', $society->id)
            ->first();

        if (!$selectedConference) {
            return response()->json(['error' => 'Conference not found'], 404);
        }

        // Registration Statistics
        $totalRegistrations = ConferenceRegistration::where('conference_id', $conferenceId)
            ->where('verified_status', 1)
            ->count();
        
        $pendingRegistrations = ConferenceRegistration::where('conference_id', $conferenceId)
            ->where('verified_status', 0)
            ->count();

        // Submission Statistics
        $totalSubmissions = Submission::where('conference_id', $conferenceId)->count();
        
        $acceptedSubmissions = Submission::where('conference_id', $conferenceId)
            ->where('review_status', 1)
            ->count();

        $pendingSubmissions = Submission::where('conference_id', $conferenceId)
            ->where('review_status', 0)
            ->count();
        
        $rejectedSubmissions = Submission::where('conference_id', $conferenceId)
            ->where('review_status', 2)
            ->count();

        // Workshop Statistics
        $workshopIds = Workshop::where('conference_id', $conferenceId)->pluck('id');
        $workshopCount = $workshopIds->count();
        $workshopRegistrations = WorkshopRegistration::whereIn('workshop_id', $workshopIds)->count();

        // Recent Activity - Last 6 months registrations by month
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        $monthlyRegistrations = ConferenceRegistration::where('conference_id', $conferenceId)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->where('verified_status', 1)
            ->select(
                DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month"),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'conferenceId' => $selectedConference->id,
                'conferenceHashid' => $selectedConference->getRouteKey(),
                'totalRegistrations' => $totalRegistrations,
                'pendingRegistrations' => $pendingRegistrations,
                'totalSubmissions' => $totalSubmissions,
                'acceptedSubmissions' => $acceptedSubmissions,
                'pendingSubmissions' => $pendingSubmissions,
                'rejectedSubmissions' => $rejectedSubmissions,
                'workshopCount' => $workshopCount,
                'workshopRegistrations' => $workshopRegistrations,
                'monthlyRegistrations' => $monthlyRegistrations,
                'conferenceName' => $selectedConference->conference_name,
                'conferenceTheme' => $selectedConference->conference_theme,
                'startDate' => $selectedConference->start_date,
                'endDate' => $selectedConference->end_date,
            ]
        ]);
    }

    public function viewDetailByAdmin($slug)
    {
        $society = Society::whereSlug($slug)->first();
        session(['societyDetail' => $society]);
        $societyDetail = society_detail();

        // Basic Counts
        $conferenceCount = Conference::where([
            'society_id' => $societyDetail->id,
            'status' => 1
        ])->count();
        
        $activeConferenceCount = Conference::where([
            'society_id' => $societyDetail->id,
            'status' => 1,
            'is_archived' => 0
        ])->count();
        
        $typeCount = MemberType::where([
            'society_id' => $societyDetail->id,
            'status' => 1
        ])->count();

        // Get conference IDs for this society
        $conferenceIds = Conference::where('society_id', $societyDetail->id)->pluck('id');

        // Registration Statistics
        $totalRegistrations = ConferenceRegistration::whereIn('conference_id', $conferenceIds)
            ->where('verified_status', 1)
            ->count();
        
        $pendingRegistrations = ConferenceRegistration::whereIn('conference_id', $conferenceIds)
            ->where('verified_status', 0)
            ->count();

        // Submission Statistics
        $totalSubmissions = Submission::whereIn('conference_id', $conferenceIds)->count();
        
        $acceptedSubmissions = Submission::whereIn('conference_id', $conferenceIds)
            ->where('review_status', 1)
            ->count();

        $pendingSubmissions = Submission::whereIn('conference_id', $conferenceIds)
            ->where('review_status', 0)
            ->count();
        
        $rejectedSubmissions = Submission::whereIn('conference_id', $conferenceIds)
            ->where('review_status', 2)
            ->count();

        // Workshop Statistics
        $workshopIds = Workshop::whereIn('conference_id', $conferenceIds)->pluck('id');
        $workshopCount = $workshopIds->count();
        $workshopRegistrations = WorkshopRegistration::whereIn('workshop_id', $workshopIds)->count();

        // Conference status counts
        $ongoingConferences = Conference::where('society_id', $societyDetail->id)
            ->where('status', 1)
            ->where('is_archived', 0)
            ->whereDate('start_date', '<=', Carbon::now())
            ->whereDate('end_date', '>=', Carbon::now())
            ->count();

        $completedConferences = Conference::where('society_id', $societyDetail->id)
            ->where('status', 1)
            ->whereDate('end_date', '<', Carbon::now())
            ->count();
        
        $upcomingConferences = Conference::where('society_id', $societyDetail->id)
            ->where('status', 1)
            ->where('is_archived', 0)
            ->where('start_date', '>=', Carbon::now())
            ->orderBy('start_date', 'asc')
            ->count();

        // Recent Activity - Last 6 months registrations by month
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        $monthlyRegistrations = ConferenceRegistration::whereIn('conference_id', $conferenceIds)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->where('verified_status', 1)
            ->select(
                DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month"),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Conference registration breakdown by conference
        $conferenceRegistrationData = Conference::where('society_id', $societyDetail->id)
            ->where('status', 1)
            ->where('is_archived', 0)
            ->withCount(['conferenceRegistrations' => function($query) {
                $query->where('verified_status', 1);
            }])
            ->orderBy('conference_registrations_count', 'desc')
            ->limit(5)
            ->get();

        // Conferences with details
        $conferences = Conference::where([
            'society_id' => $societyDetail->id,
            'is_archived' => 0,
            'status' => 1
        ])->with(['ConferenceVenueDetail', 'ConferenceOrganizer'])
        ->withCount(['conferenceRegistrations' => function($query) {
            $query->where('verified_status', 1);
        }])
        ->withCount(['submissions'])
        ->orderBy('start_date', 'desc')
        ->limit(10)
        ->get();

        return view('backend.users.societies.dashboard', compact(
            'conferenceCount',
            'activeConferenceCount',
            'typeCount',
            'totalRegistrations',
            'pendingRegistrations',
            'totalSubmissions',
            'acceptedSubmissions',
            'pendingSubmissions',
            'rejectedSubmissions',
            'workshopCount',
            'workshopRegistrations',
            'ongoingConferences',
            'completedConferences',
            'upcomingConferences',
            'monthlyRegistrations',
            'conferenceRegistrationData',
            'conferences',
            'upcomingConferences'
        ))->with('society', $societyDetail);
    }
}
