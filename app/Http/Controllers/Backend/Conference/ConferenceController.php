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
use App\Models\Conference\SubmissionCategoryMajorTrack;
use App\Models\User;
use App\Models\User\Society;
use App\Models\Workshop\Workshop;
use App\Models\Workshop\WorkshopRegistration;
use App\Services\File\FileService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;

class ConferenceController extends Controller
{
    /**  
     * Display a listing of the resource.
     */
    public function __construct(protected FileService $file_service) {
        
    }

    public function index($society)
    {
        $activeConferences = Conference::where([
            'society_id' => $society->id,
            'status' => 1,
            'is_archived' => 0
        ])->orderBy('start_date', 'desc')->get();

        $archivedConferences = Conference::where([
            'society_id' => $society->id,
            'status' => 1,
            'is_archived' => 1
        ])->orderBy('archived_at', 'desc')->get();

        return view('backend.conference.index', compact('activeConferences', 'archivedConferences', 'society'));
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
            if (!empty($req['tags'])) {

                $tagArray = json_decode($request->tags, true);
                $req['tags']  = is_array($tagArray)
                    ? implode(',', array_column($tagArray, 'value'))
                    : '';
            }
            $req['society_id'] = $society->id;

            //slugify function is custom helper function 
            $req['slug'] = Str::slug($req['conference_name']);

            DB::beginTransaction();

            //uploading the conference logo
            if (!empty($req['conference_logo'])) {
                //file uplaod function parameter required file,name,location
                $req['conference_logo'] = $this->file_service->fileUpload($req['conference_logo'], 'conference_logo', 'conference/conference/logo');
            }

            //uploading the conference banner
            if (!empty($req['conference_banner'])) {
                //file uplaod function parameter required file,name,location
                $req['conference_banner'] = $this->file_service->fileUpload($req['conference_banner'], 'conference_banner', 'conference/conference/banner');
            }
 
            //uploading the organizer logo
            if (!empty($req['organizer_logo'])) {
                //file uplaod function parameter required file,name,location
                $req['organizer_logo'] = $this->file_service->fileUpload($req['organizer_logo'], 'organizer_logo', 'conference/organizer/logo');
            }

            // Handle multiple partner logos
            if (!empty($request->file('partner_logos'))) {
                $partnerLogos = [];
                foreach ($request->file('partner_logos') as $index => $logo) {
                    $fileName = $this->file_service->fileUpload($logo, 'partner_logo_' . $index, 'conference/partner-logos');
                    $partnerLogos[] = $fileName;
                }
                $req['partner_logos'] = $partnerLogos; // No json_encode - model cast handles it
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

            if (!empty($req['tags'])) {
                $tagArray = json_decode($request->tags, true);
                $req['tags']  = is_array($tagArray)
                    ? implode(',', array_column($tagArray, 'value'))
                    : '';
            }
            //slugify function is custom helper function
            $req['slug'] = Str::slug($req['conference_name']);

            DB::beginTransaction();

            //uploading the conference logo
            if (!empty($req['conference_logo'])) {
                $this->file_service->deleteFile($conference->conference_logo, 'conference/conference/logo');
                //file uplaod function parameter required file,name,location
                $req['conference_logo'] = $this->file_service->fileUpload($req['conference_logo'], 'conference_logo', 'conference/conference/logo');
            }

            //uploading the conference banner
            if (!empty($req['conference_banner'])) {
                $this->file_service->deleteFile($conference->conference_banner, 'conference/conference/banner');
                //file uplaod function parameter required file,name,location
                $req['conference_banner'] = $this->file_service->fileUpload($req['conference_banner'], 'conference_banner', 'conference/conference/banner');
            }

            //uploading the organizer logo
            if (!empty($req['organizer_logo'])) {
                //deleting the file deleteFile function parameter required file,location
                $this->file_service->deleteFile($conference->organizer_logo, 'conference/organizer/logo');
                //file uplaod function parameter required file,name,location
                $req['organizer_logo'] = $this->file_service->fileUpload($req['organizer_logo'], 'organizer_logo', 'conference/organizer/logo');
            }

            // Handle partner logos update
            $existingPartnerLogos = is_array($conference->partner_logos) ? $conference->partner_logos : [];

            // Delete logos marked for deletion
            if (!empty($request->deleted_partner_logos)) {
                $deletedLogos = json_decode($request->deleted_partner_logos, true) ?? [];
                if (is_array($deletedLogos)) {
                    foreach ($deletedLogos as $logo) {
                        $this->file_service->deleteFile($logo, 'conference/partner-logos');
                        $existingPartnerLogos = array_diff($existingPartnerLogos, [$logo]);
                    }
                    $existingPartnerLogos = array_values($existingPartnerLogos); // Re-index array
                }
            }

            // Upload new partner logos
            if (!empty($request->file('partner_logos'))) {
                foreach ($request->file('partner_logos') as $index => $logo) {
                    $fileName = $this->file_service->fileUpload($logo, 'partner_logo_' . time() . '_' . $index, 'conference/partner-logos');
                    $existingPartnerLogos[] = $fileName;
                }
            }

            $req['partner_logos'] = !empty($existingPartnerLogos) ? $existingPartnerLogos : null; // No json_encode - model cast handles it

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
        // dd('da');
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

        // Get add-ons statistics
        $addonStats = DB::table('conference_registration_addons as cra')
            ->join('conference_addons as ca', 'cra.conference_addon_id', '=', 'ca.id')
            ->join('conference_registrations as cr', 'cra.conference_registration_id', '=', 'cr.id')
            ->where('cr.conference_id', $conference->id)
            ->where('cra.status', 1)
            ->select(
                'ca.id',
                'ca.addon_name',
                DB::raw('COUNT(*) as total_count'),
                DB::raw('SUM(CASE WHEN cra.include_for_guests = true THEN 1 ELSE 0 END) as guest_count'),
                DB::raw('SUM(CASE WHEN cra.include_for_guests = false THEN 1 ELSE 0 END) as participant_count'),
                DB::raw('SUM(CAST(cra.amount as DECIMAL(10,2))) as total_revenue')
            )
            ->groupBy('ca.id', 'ca.addon_name')
            ->get();

        // Get total add-ons count
        $totalAddons = DB::table('conference_registration_addons as cra')
            ->join('conference_registrations as cr', 'cra.conference_registration_id', '=', 'cr.id')
            ->where('cr.conference_id', $conference->id)
            ->where('cra.status', 1)
            ->count();

        // Get accompanying persons statistics
        $accompanyingStats = DB::table('accompany_people as ap')
            ->join('conference_registrations as cr', 'ap.conference_registration_id', '=', 'cr.id')
            ->where('cr.conference_id', $conference->id)
            ->where('ap.status', 1)
            ->select(
                DB::raw('COUNT(*) as total_count')
            )
            ->first();

        $totalAccompanyingPersons = $accompanyingStats->total_count ?? 0;


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

        // Get sponsor attendance and meal data
        $sponsorData = DB::table('sponsors')
            ->leftJoin('sponsor_attendances', 'sponsors.id', '=', 'sponsor_attendances.sponsor_id')
            ->leftJoin('sponsor_meals', 'sponsors.id', '=', 'sponsor_meals.sponsor_id')
            ->select(
                DB::raw('COUNT(DISTINCT sponsor_attendances.id) as sponsor_attendance_count'),
                DB::raw('COALESCE(SUM(sponsor_meals.lunch_taken), 0) as sponsor_lunch_count'),
                DB::raw('COALESCE(SUM(sponsor_meals.dinner_taken), 0) as sponsor_dinner_count')
            )
            ->where('sponsors.conference_id', $conferenceId)
            ->where('sponsors.status', 1)
            ->first();

        $startDate = Carbon::parse($conference->start_date);
        $endDate = Carbon::parse($conference->end_date);

        $dates = [];
        while ($startDate->lte($endDate)) {
            $dates[] = $startDate->toDateString();
            $startDate->addDay();
        }
        $workshops = Workshop::where(['conference_id' => $conference->id, 'status' => 1])->get();
        $workshopMealCounts = DB::table('workshop_registrations as wr')
            ->join('workshops as w', 'w.id', '=', 'wr.workshop_id')
            ->where('w.conference_id', $conference->id)
            ->where('wr.status', 1)
            ->where('wr.registrant_type', 1)
            ->select(
                'wr.workshop_id',
                DB::raw("SUM(CASE WHEN wr.meal_type = 1 THEN 1 ELSE 0 END) as veg"),
                DB::raw("SUM(CASE WHEN wr.meal_type = 2 THEN 1 ELSE 0 END) as nonVeg"),
                DB::raw("COUNT(*) as total")
            )
            ->groupBy('wr.workshop_id')
            ->get()
            ->keyBy('workshop_id');
        $submissionCount = Submission::where(['conference_id' => $conference->id, 'user_id' => current_user()->id, 'status' => 1])->count();
        $workshop = Workshop::where(['conference_id' => $conference->id, 'status' => 1])->pluck('id');
        $workshopRegistrationCount = WorkshopRegistration::where(['user_id' => current_user()->id, 'registrant_type' => 1, 'status' => 1])->whereIn('workshop_id', $workshop)->count();
        // Count submissions where current user is assigned as expert/reviewer
        $reviewAssignmentCount = Submission::where(['conference_id' => $conference->id, 'expert_id' => current_user()->id, 'status' => 1])->count();
        $submissionCategoryMajorTracks = SubmissionCategoryMajorTrack::where(['conference_id' => $conference->id, 'status' => 1])->get();

        // Get current user's addon and accompanying person info (for participants)
        $userRegistration = ConferenceRegistration::where(['conference_id' => $conference->id, 'user_id' => current_user()->id, 'status' => 1])->first();
        $userAddons = [];
        $userAccompanyingPersons = [];
        if ($userRegistration) {
            $userAddons = DB::table('conference_registration_addons as cra')
                ->join('conference_addons as ca', 'cra.conference_addon_id', '=', 'ca.id')
                ->where('cra.conference_registration_id', $userRegistration->id)
                ->where('cra.status', 1)
                ->select('ca.addon_name', 'cra.amount', 'cra.include_for_guests', 'cra.conference_addon_id')
                ->orderBy('ca.addon_name')
                ->orderBy('cra.include_for_guests')
                ->get();
            
            $userAccompanyingPersons = DB::table('accompany_people')
                ->where('conference_registration_id', $userRegistration->id)
                ->where('status', 1)
                ->get();
        }

        // Check if user is eligible for conference certificate
        $canDownloadConferenceCertificate = false;
        if ($userRegistration) {
            // Check if conference has ended
            $conferenceHasEnded = now()->isAfter($conference->end_date);
            
            // Check if conference has certificate settings
            $hasCertificateSetting = $conference->conferenceCertificate()->exists();
            
            // Check if user has attended the conference
            $hasAttendance = $userRegistration->attendances()->exists();
            
            $canDownloadConferenceCertificate = $conferenceHasEnded && $hasCertificateSetting && $hasAttendance;
        }

        // Check if user is eligible for workshop certificates
        $eligibleWorkshopCertificates = [];
        if (feature_enabled('workshop-management', getSociety(request()->segment(2)))) {
            $userWorkshopRegistrations = WorkshopRegistration::where([
                'user_id' => current_user()->id,
                // 'registrant_type' => 1, // Participant
                'status' => 1
            ])->whereIn('workshop_id', $workshop)->get();

            foreach ($userWorkshopRegistrations as $workshopReg) {
                $workshopModel = Workshop::find($workshopReg->workshop_id);
                
                if ($workshopModel) {
                    // Check if workshop has ended
                    $workshopHasEnded = now()->isAfter($workshopModel->end_date);
                    
                    // Check if workshop has certificate settings
                    $hasWorkshopCertificateSetting = $workshopModel->workshopCertificate()->exists();
                    
                    // Check if user has attended the workshop
                    // $hasWorkshopAttendance = $workshopReg->attendances()->exists();
                    
                    if ($workshopHasEnded && $hasWorkshopCertificateSetting) {
                        $eligibleWorkshopCertificates[] = [
                            'workshop' => $workshopModel,
                            'registration' => $workshopReg
                        ];
                    }
                }
            }
        }

        return view('backend.conference.dashboard', compact('conferenceRegistrationCount', 'totalNationalRegistrants', 'totalInternationalRegistrants', 'mealCounts', 'conference', 'society', 'data', 'sponsorData', 'dates', 'workshops', 'workshopMealCounts', 'submissionCount', 'workshopRegistrationCount', 'submissionCategoryMajorTracks', 'addonStats', 'totalAddons', 'totalAccompanyingPersons', 'userAddons', 'userAccompanyingPersons', 'reviewAssignmentCount', 'canDownloadConferenceCertificate', 'userRegistration', 'eligibleWorkshopCertificates'));
    }

    public function submissionsChart(Request $request, $society, $conference)
    {
        $categoryId = $request->input('category_id');

        $query = Submission::where(['conference_id' => $conference->id, 'status' => 1]);
        // dd($query);
        if ($categoryId) {
            $query->where('submission_category_major_track_id', $categoryId);
        }

        $counts = $query->selectRaw('presentation_type, COUNT(*) as total')
            ->groupBy('presentation_type')
            ->pluck('total', 'presentation_type');

        return response()->json([
            'poster' => $counts[1] ?? 0,
            'oral'   => $counts[2] ?? 0,
        ]);
    }

    public function viewAttendanceStatus(Request $request, $society, $conference)
    {
        $viewType = $request->get('view_type', 'registrants'); // registrants, sponsors, or both
        $registrants = collect();
        $sponsors = collect();

        // Fetch Registrants Data
        if (in_array($viewType, ['registrants', 'both'])) {
            $query = DB::table('conference_registrations as CR')
                ->select(
                    'CR.id',
                    'CR.status',
                    'CR.conference_id',
                    'CR.total_attendee',
                    'CR.verified_status',
                    'CR.registrant_type',
                    'CR.attend_type',
                    'CR.meal_type',
                    'CR.registration_id',
                    'CR.created_at as registration_date',
                    'U.f_name',
                    'U.m_name',
                    'U.l_name',
                    'U.email',
                    'UD.phone',
                    'UD.country_id',
                    'UD.institution_id',
                    'C.country_name as country_name',
                    'I.name as institution_name',
                    DB::raw("'registrant' as record_type")
                )
                ->where([
                    'CR.verified_status' => 1,
                    'CR.status' => 1,
                    'CR.conference_id' => $conference->id,
                ])
                ->join('users as U', 'CR.user_id', '=', 'U.id')
                ->join('user_details as UD', 'U.id', '=', 'UD.user_id')
                ->leftJoin('countries as C', 'UD.country_id', '=', 'C.id')
                ->leftJoin('institutions as I', 'UD.institution_id', '=', 'I.id');

        // Apply filters
        if ($request->filled('date') && $request->date !== 'all') {
            $query->where(function($q) use ($request) {
                $q->whereExists(function($subQuery) use ($request) {
                    $subQuery->select(DB::raw(1))
                        ->from('attendances')
                        ->whereColumn('attendances.conference_registration_id', 'CR.id')
                        ->whereDate('attendances.created_at', $request->date);
                })
                ->orWhereExists(function($subQuery) use ($request) {
                    $subQuery->select(DB::raw(1))
                        ->from('meals')
                        ->whereColumn('meals.conference_registration_id', 'CR.id')
                        ->whereDate('meals.created_at', $request->date);
                });
            });
        }

        if ($request->filled('country') && $request->country !== 'all') {
            $query->where('UD.country_id', $request->country);
        }

        if ($request->filled('kit_status') && $request->kit_status !== 'all') {
            if ($request->kit_status == 'taken') {
                $query->whereExists(function($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('conference_registration_kits')
                        ->whereColumn('conference_registration_kits.conference_registration_id', 'CR.id')
                        ->where('conference_registration_kits.status', 1);
                });
            } else {
                $query->whereNotExists(function($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('conference_registration_kits')
                        ->whereColumn('conference_registration_kits.conference_registration_id', 'CR.id')
                        ->where('conference_registration_kits.status', 1);
                });
            }
        }

        if ($request->filled('registrant_type') && $request->registrant_type !== 'all') {
            $query->where('CR.registrant_type', $request->registrant_type);
        }

        if ($request->filled('has_accompany') && $request->has_accompany !== 'all') {
            if ($request->has_accompany == 'yes') {
                $query->where('CR.total_attendee', '>', 1);
            } else {
                $query->where('CR.total_attendee', '=', 1);
            }
        }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('U.f_name', 'ILIKE', "%{$search}%")
                      ->orWhere('U.l_name', 'ILIKE', "%{$search}%")
                      ->orWhere('U.email', 'ILIKE', "%{$search}%")
                      ->orWhere('CR.registration_id', 'ILIKE', "%{$search}%");
                });
            }

            $query->orderBy('U.f_name', 'asc'); 
            
            $registrants = $query->get();
        }

        // Fetch Sponsors Data
        if (in_array($viewType, ['sponsors', 'both'])) {
            $sponsorQuery = DB::table('sponsors as S')
                ->select(
                    'S.id',
                    'S.name as sponsor_name',
                    'S.email',
                    'S.phone',
                    'S.contact_person',
                    'S.total_attendee',
                    'S.registration_id',
                    'S.lunch_access',
                    'S.dinner_access',
                    'S.created_at as registration_date',
                    'SC.category_name as category_name',
                    DB::raw("'N/A' as country_name"),
                    DB::raw("'sponsor' as record_type")
                )
                ->where([
                    'S.status' => 1,
                    'S.conference_id' => $conference->id,
                ])
                ->leftJoin('sponsor_categories as SC', 'S.sponsor_category_id', '=', 'SC.id');

            // Apply filters for sponsors
            if ($request->filled('date') && $request->date !== 'all') {
                $sponsorQuery->where(function($q) use ($request) {
                    $q->whereExists(function($subQuery) use ($request) {
                        $subQuery->select(DB::raw(1))
                            ->from('sponsor_attendances')
                            ->whereColumn('sponsor_attendances.sponsor_id', 'S.id')
                            ->whereDate('sponsor_attendances.created_at', $request->date);
                    })
                    ->orWhereExists(function($subQuery) use ($request) {
                        $subQuery->select(DB::raw(1))
                            ->from('sponsor_meals')
                            ->whereColumn('sponsor_meals.sponsor_id', 'S.id')
                            ->whereDate('sponsor_meals.created_at', $request->date);
                    });
                });
            }

            // Kit status filter (sponsors don't have kits, so exclude them if filtering by kit)
            if ($request->filled('kit_status') && $request->kit_status !== 'all') {
                // Skip sponsors when filtering by kit status as they don't have kits
                if ($viewType === 'sponsors') {
                    $sponsorQuery->whereRaw('1 = 0'); // Return no results
                }
            }

            if ($request->filled('has_accompany') && $request->has_accompany !== 'all') {
                if ($request->has_accompany == 'yes') {
                    $sponsorQuery->where('S.total_attendee', '>', 1);
                } else {
                    $sponsorQuery->where('S.total_attendee', '=', 1);
                }
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $sponsorQuery->where(function($q) use ($search) {
                    $q->where('S.name', 'ILIKE', "%{$search}%")
                      ->orWhere('S.email', 'ILIKE', "%{$search}%")
                      ->orWhere('S.contact_person', 'ILIKE', "%{$search}%")
                      ->orWhere('S.registration_id', 'ILIKE', "%{$search}%");
                });
            }

            $sponsorQuery->orderBy('S.name', 'asc');
            
            $sponsors = $sponsorQuery->get();
        }

        // Attach meals, attendance, kit and accompanying persons for REGISTRANTS
        foreach ($registrants as $registrant) {
            // Meals
            $mealsQuery = DB::table('meals')
                ->where('conference_registration_id', $registrant->id)
                ->select('id', 'lunch_taken', 'dinner_taken', 'created_at', 'updated_at')
                ->orderBy('created_at', 'desc');
            
            if ($request->filled('date') && $request->date !== 'all') {
                $mealsQuery->whereDate('created_at', $request->date);
            }
            
            $meals = $mealsQuery->get();

            // Attendances
            $attendancesQuery = DB::table('attendances')
                ->where('conference_registration_id', $registrant->id)
                ->select('id', 'status', 'created_at', 'updated_at')
                ->orderBy('created_at', 'desc');
            
            if ($request->filled('date') && $request->date !== 'all') {
                $attendancesQuery->whereDate('created_at', $request->date);
            }
            
            $attendences = $attendancesQuery->get();

            // Accompanying Persons
            $accompanyPersons = DB::table('accompany_people')
                ->where('conference_registration_id', $registrant->id)
                ->where('status', 1)
                ->select('id', 'person_name', 'created_at')
                ->get();

            // Conference Kit
            $conferenceRegistrationKit = DB::table('conference_registration_kits')
                ->where('conference_registration_id', $registrant->id)
                ->where('status', 1)
                ->first();

            $registrant->meals = $meals;
            $registrant->attendences = $attendences;
            $registrant->accompanyPersons = $accompanyPersons;
            $registrant->conferenceRegistrationKit = $conferenceRegistrationKit;
            
            // Calculate total meals consumed
            $registrant->total_lunch_consumed = $meals->sum('lunch_taken');
            $registrant->total_dinner_consumed = $meals->sum('dinner_taken');
            $registrant->total_attendance_count = $attendences->count();
        }

        // Attach meals and attendance for SPONSORS
        foreach ($sponsors as $sponsor) {
            // Meals
            $mealsQuery = DB::table('sponsor_meals')
                ->where('sponsor_id', $sponsor->id)
                ->select('id', 'lunch_taken', 'dinner_taken', 'created_at', 'updated_at')
                ->orderBy('created_at', 'desc');
            
            if ($request->filled('date') && $request->date !== 'all') {
                $mealsQuery->whereDate('created_at', $request->date);
            }
            
            $meals = $mealsQuery->get();

            // Attendances
            $attendancesQuery = DB::table('sponsor_attendances')
                ->where('sponsor_id', $sponsor->id)
                ->select('id', 'created_at', 'updated_at')
                ->orderBy('created_at', 'desc');
            
            if ($request->filled('date') && $request->date !== 'all') {
                $attendancesQuery->whereDate('created_at', $request->date);
            }
            
            $attendences = $attendancesQuery->get();

            $sponsor->meals = $meals;
            $sponsor->attendences = $attendences;
            $sponsor->accompanyPersons = []; // Sponsors don't have accompanying persons
            $sponsor->conferenceRegistrationKit = null; // Sponsors don't have kits
            
            // Calculate total meals consumed
            $sponsor->total_lunch_consumed = $meals->sum('lunch_taken');
            $sponsor->total_dinner_consumed = $meals->sum('dinner_taken');
            $sponsor->total_attendance_count = $attendences->count();
        }

        // Merge data for view
        $allData = $registrants->merge($sponsors);

        // Get unique countries for filter
        $countries = DB::table('conference_registrations as CR')
            ->join('users as U', 'CR.user_id', '=', 'U.id')
            ->join('user_details as UD', 'U.id', '=', 'UD.user_id')
            ->join('countries as C', 'UD.country_id', '=', 'C.id')
            ->where('CR.conference_id', $conference->id)
            ->where('CR.status', 1)
            ->where('CR.verified_status', 1)
            ->select('C.id', 'C.country_name')
            ->distinct()
            ->orderBy('C.country_name')
            ->get();

        // Get conference dates for daily filter
        $dates = [];
        $startDate = \Carbon\Carbon::parse($conference->start_date);
        $endDate = \Carbon\Carbon::parse($conference->end_date);
        
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }
        
        return view('backend.conference.attendance-status', compact('allData', 'registrants', 'sponsors', 'conference', 'society', 'countries', 'dates', 'viewType'));
    }

    public function exportAttendanceStatus(Request $request, $society, $conference)
    {
        $viewType = $request->get('view_type', 'registrants');
        $registrants = collect();
        $sponsors = collect();

        // Fetch Registrants (same filtering logic as viewAttendanceStatus)
        if (in_array($viewType, ['registrants', 'both'])) {
            $query = DB::table('conference_registrations as CR')
                ->select(
                    'CR.id',
                    'CR.status',
                    'CR.conference_id',
                    'CR.total_attendee',
                    'CR.verified_status',
                    'CR.registrant_type',
                    'CR.attend_type',
                    'CR.meal_type',
                    'CR.registration_id',
                    'CR.created_at as registration_date',
                    'U.f_name',
                    'U.m_name',
                    'U.l_name',
                    'U.email',
                    'UD.phone',
                    'UD.country_id',
                    'UD.institution_id',
                    'C.country_name as country_name',
                    'I.name as institution_name',
                    DB::raw("'registrant' as record_type")
                )
                ->where([
                    'CR.verified_status' => 1,
                    'CR.status' => 1,
                    'CR.conference_id' => $conference->id,
                ])
                ->join('users as U', 'CR.user_id', '=', 'U.id')
                ->join('user_details as UD', 'U.id', '=', 'UD.user_id')
                ->leftJoin('countries as C', 'UD.country_id', '=', 'C.id')
                ->leftJoin('institutions as I', 'UD.institution_id', '=', 'I.id');

            // Apply filters
            if ($request->filled('date') && $request->date !== 'all') {
                $query->where(function($q) use ($request) {
                    $q->whereExists(function($subQuery) use ($request) {
                        $subQuery->select(DB::raw(1))
                            ->from('attendances')
                            ->whereColumn('attendances.conference_registration_id', 'CR.id')
                            ->whereDate('attendances.created_at', $request->date);
                    })
                    ->orWhereExists(function($subQuery) use ($request) {
                        $subQuery->select(DB::raw(1))
                            ->from('meals')
                            ->whereColumn('meals.conference_registration_id', 'CR.id')
                            ->whereDate('meals.created_at', $request->date);
                    });
                });
            }

            if ($request->filled('country') && $request->country !== 'all') {
                $query->where('UD.country_id', $request->country);
            }

            if ($request->filled('kit_status') && $request->kit_status !== 'all') {
                if ($request->kit_status == 'taken') {
                    $query->whereExists(function($subQuery) {
                        $subQuery->select(DB::raw(1))
                            ->from('conference_registration_kits')
                            ->whereColumn('conference_registration_kits.conference_registration_id', 'CR.id')
                            ->where('conference_registration_kits.status', 1);
                    });
                } else {
                    $query->whereNotExists(function($subQuery) {
                        $subQuery->select(DB::raw(1))
                            ->from('conference_registration_kits')
                            ->whereColumn('conference_registration_kits.conference_registration_id', 'CR.id')
                            ->where('conference_registration_kits.status', 1);
                    });
                }
            }

            if ($request->filled('registrant_type') && $request->registrant_type !== 'all') {
                $query->where('CR.registrant_type', $request->registrant_type);
            }

            if ($request->filled('has_accompany') && $request->has_accompany !== 'all') {
                if ($request->has_accompany == 'yes') {
                    $query->where('CR.total_attendee', '>', 1);
                } else {
                    $query->where('CR.total_attendee', '=', 1);
                }
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('U.f_name', 'ILIKE', "%{$search}%")
                      ->orWhere('U.l_name', 'ILIKE', "%{$search}%")
                      ->orWhere('U.email', 'ILIKE', "%{$search}%")
                      ->orWhere('CR.registration_id', 'ILIKE', "%{$search}%");
                });
            }

            $query->orderBy('U.f_name', 'asc');
            $registrants = $query->get();

            // Attach data for registrants
            foreach ($registrants as $registrant) {
                $mealsQuery = DB::table('meals')
                    ->where('conference_registration_id', $registrant->id)
                    ->select('id', 'lunch_taken', 'dinner_taken', 'created_at', 'updated_at')
                    ->orderBy('created_at', 'desc');
                
                if ($request->filled('date') && $request->date !== 'all') {
                    $mealsQuery->whereDate('created_at', $request->date);
                }
                
                $meals = $mealsQuery->get();

                $attendancesQuery = DB::table('attendances')
                    ->where('conference_registration_id', $registrant->id)
                    ->select('id', 'status', 'created_at', 'updated_at')
                    ->orderBy('created_at', 'desc');
                
                if ($request->filled('date') && $request->date !== 'all') {
                    $attendancesQuery->whereDate('created_at', $request->date);
                }
                
                $attendences = $attendancesQuery->get();

                $accompanyPersons = DB::table('accompany_people')
                    ->where('conference_registration_id', $registrant->id)
                    ->where('status', 1)
                    ->select('id', 'person_name', 'created_at')
                    ->get();

                $conferenceRegistrationKit = DB::table('conference_registration_kits')
                    ->where('conference_registration_id', $registrant->id)
                    ->where('status', 1)
                    ->first();

                $registrant->meals = $meals;
                $registrant->attendences = $attendences;
                $registrant->accompanyPersons = $accompanyPersons;
                $registrant->conferenceRegistrationKit = $conferenceRegistrationKit;
                
                $registrant->total_lunch_consumed = $meals->sum('lunch_taken');
                $registrant->total_dinner_consumed = $meals->sum('dinner_taken');
                $registrant->total_attendance_count = $attendences->count();
            }
        }

        // Fetch Sponsors (if needed)
        if (in_array($viewType, ['sponsors', 'both'])) {
            $sponsorQuery = DB::table('sponsors as S')
                ->select(
                    'S.id',
                    'S.name as sponsor_name',
                    'S.email',
                    'S.phone',
                    'S.contact_person',
                    'S.total_attendee',
                    'S.registration_id',
                    'S.created_at as registration_date',
                    'SC.category_name as category_name',
                    DB::raw("'N/A' as country_name"),
                    DB::raw("'sponsor' as record_type")
                )
                ->where([
                    'S.status' => 1,
                    'S.conference_id' => $conference->id,
                ])
                ->leftJoin('sponsor_categories as SC', 'S.sponsor_category_id', '=', 'SC.id');

            // Apply filters
            if ($request->filled('date') && $request->date !== 'all') {
                $sponsorQuery->where(function($q) use ($request) {
                    $q->whereExists(function($subQuery) use ($request) {
                        $subQuery->select(DB::raw(1))
                            ->from('sponsor_attendances')
                            ->whereColumn('sponsor_attendances.sponsor_id', 'S.id')
                            ->whereDate('sponsor_attendances.created_at', $request->date);
                    })
                    ->orWhereExists(function($subQuery) use ($request) {
                        $subQuery->select(DB::raw(1))
                            ->from('sponsor_meals')
                            ->whereColumn('sponsor_meals.sponsor_id', 'S.id')
                            ->whereDate('sponsor_meals.created_at', $request->date);
                    });
                });
            }

            if ($request->filled('kit_status') && $request->kit_status !== 'all') {
                if ($viewType === 'sponsors') {
                    $sponsorQuery->whereRaw('1 = 0');
                }
            }

            if ($request->filled('has_accompany') && $request->has_accompany !== 'all') {
                if ($request->has_accompany == 'yes') {
                    $sponsorQuery->where('S.total_attendee', '>', 1);
                } else {
                    $sponsorQuery->where('S.total_attendee', '=', 1);
                }
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $sponsorQuery->where(function($q) use ($search) {
                    $q->where('S.name', 'ILIKE', "%{$search}%")
                      ->orWhere('S.email', 'ILIKE', "%{$search}%")
                      ->orWhere('S.contact_person', 'ILIKE', "%{$search}%")
                      ->orWhere('S.registration_id', 'ILIKE', "%{$search}%");
                });
            }

            $sponsorQuery->orderBy('S.name', 'asc');
            $sponsors = $sponsorQuery->get();

            // Attach data for sponsors
            foreach ($sponsors as $sponsor) {
                $mealsQuery = DB::table('sponsor_meals')
                    ->where('sponsor_id', $sponsor->id)
                    ->select('id', 'lunch_taken', 'dinner_taken', 'created_at', 'updated_at')
                    ->orderBy('created_at', 'desc');
                
                if ($request->filled('date') && $request->date !== 'all') {
                    $mealsQuery->whereDate('created_at', $request->date);
                }
                
                $meals = $mealsQuery->get();

                $attendancesQuery = DB::table('sponsor_attendances')
                    ->where('sponsor_id', $sponsor->id)
                    ->select('id', 'created_at', 'updated_at')
                    ->orderBy('created_at', 'desc');
                
                if ($request->filled('date') && $request->date !== 'all') {
                    $attendancesQuery->whereDate('created_at', $request->date);
                }
                
                $attendences = $attendancesQuery->get();

                $sponsor->meals = $meals;
                $sponsor->attendences = $attendences;
                $sponsor->accompanyPersons = [];
                $sponsor->conferenceRegistrationKit = null;
                
                $sponsor->total_lunch_consumed = $meals->sum('lunch_taken');
                $sponsor->total_dinner_consumed = $meals->sum('dinner_taken');
                $sponsor->total_attendance_count = $attendences->count();
            }
        }

        // Merge both collections
        $allData = $registrants->merge($sponsors);

        $fileName = 'attendance_status_' . $conference->abbreviation . '_' . now()->format('Y-m-d_His') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\AttendanceStatusExport($allData),
            $fileName
        );
    }
    public function getStats(Request $request)
    {
        $conferenceId = $request->conference_id;
        $date = $request->date;

        // Query for conference registrations
        $query = DB::table('conference_registrations');
        
        // Filter by date if not 'all'
        if ($date && $date !== 'all') {
            $query->leftJoin('attendances', function($join) use ($date) {
                $join->on('conference_registrations.id', '=', 'attendances.conference_registration_id')
                     ->whereDate('attendances.created_at', '=', $date);
            })
            ->leftJoin('meals', function($join) use ($date) {
                $join->on('conference_registrations.id', '=', 'meals.conference_registration_id')
                     ->whereDate('meals.created_at', '=', $date);
            });
        } else {
            $query->leftJoin('attendances', 'conference_registrations.id', '=', 'attendances.conference_registration_id')
                  ->leftJoin('meals', 'conference_registrations.id', '=', 'meals.conference_registration_id');
        }
        
        $query->where('conference_registrations.conference_id', $conferenceId);

        $data = $query->select(
            DB::raw('COUNT(DISTINCT attendances.id) as attendance_count'),
            DB::raw('COALESCE(SUM(meals.lunch_taken), 0) as lunch_count'),
            DB::raw('COALESCE(SUM(meals.dinner_taken), 0) as dinner_count')
        )->first();

        // Query for kit distribution
        $kitQuery = DB::table('conference_registration_kits');
        
        // Filter by date if not 'all'
        if ($date && $date !== 'all') {
            $kitQuery->whereDate('conference_registration_kits.created_at', '=', $date);
        }
        
        $kitQuery->join('conference_registrations', 'conference_registration_kits.conference_registration_id', '=', 'conference_registrations.id')
                 ->where('conference_registrations.conference_id', $conferenceId)
                 ->where('conference_registration_kits.status', 1);

        $kitData = $kitQuery->count();

        // Query for sponsor data
        $sponsorQuery = DB::table('sponsors');
        
        // Filter by date if not 'all'
        if ($date && $date !== 'all') {
            $sponsorQuery->leftJoin('sponsor_attendances', function($join) use ($date) {
                $join->on('sponsors.id', '=', 'sponsor_attendances.sponsor_id')
                     ->whereDate('sponsor_attendances.created_at', '=', $date);
            })
            ->leftJoin('sponsor_meals', function($join) use ($date) {
                $join->on('sponsors.id', '=', 'sponsor_meals.sponsor_id')
                     ->whereDate('sponsor_meals.created_at', '=', $date);
            });
        } else {
            $sponsorQuery->leftJoin('sponsor_attendances', 'sponsors.id', '=', 'sponsor_attendances.sponsor_id')
                         ->leftJoin('sponsor_meals', 'sponsors.id', '=', 'sponsor_meals.sponsor_id');
        }
        
        $sponsorQuery->where('sponsors.conference_id', $conferenceId)
                     ->where('sponsors.status', 1);

        $sponsorData = $sponsorQuery->select(
            DB::raw('COUNT(DISTINCT sponsor_attendances.id) as sponsor_attendance_count'),
            DB::raw('COALESCE(SUM(sponsor_meals.lunch_taken), 0) as sponsor_lunch_count'),
            DB::raw('COALESCE(SUM(sponsor_meals.dinner_taken), 0) as sponsor_dinner_count')
        )->first();

        // Combine conference registrants and sponsors counts
        $totalAttendance = ($data->attendance_count ?? 0) + ($sponsorData->sponsor_attendance_count ?? 0);
        $totalLunch = ($data->lunch_count ?? 0) + ($sponsorData->sponsor_lunch_count ?? 0);
        $totalDinner = ($data->dinner_count ?? 0) + ($sponsorData->sponsor_dinner_count ?? 0);

        // Return combined totals with breakdown
        return response()->json([
            'attendance_count' => $totalAttendance, 
            'lunch_count' => $totalLunch,
            'dinner_count' => $totalDinner,
            'kit_count' => $kitData,
            'registrant_attendance_count' => $data->attendance_count ?? 0,
            'registrant_lunch_count' => $data->lunch_count ?? 0,
            'registrant_dinner_count' => $data->dinner_count ?? 0,
            'sponsor_attendance_count' => $sponsorData->sponsor_attendance_count ?? 0,
            'sponsor_lunch_count' => $sponsorData->sponsor_lunch_count ?? 0,
            'sponsor_dinner_count' => $sponsorData->sponsor_dinner_count ?? 0,
        ]);
    }

    /**
     * Archive the specified conference.
     */
    public function archive($society, Conference $conference)
    {
        try {
            $conference->update([
                'is_archived' => 1,
                'archived_at' => now()
            ]);

            return redirect()->route('conference.index', $society)
                ->with('status', 'Conference archived successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('delete', 'Failed to archive conference');
        }
    }

    /**
     * Unarchive the specified conference.
     */
    public function unarchive($society, Conference $conference)
    {
        try {
            $conference->update([
                'is_archived' => 0,
                'archived_at' => null
            ]);

            return redirect()->route('conference.index', $society)
                ->with('status', 'Conference unarchived successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('delete', 'Failed to unarchive conference');
        }
    }
}
