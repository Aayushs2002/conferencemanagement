<?php

namespace App\Http\Controllers\Backend\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\Conference\PassSetting;
use App\Models\Sponsor\Sponsor;
use App\Models\Sponsor\SponsorAttendance;
use App\Models\Sponsor\SponsorCategory;
use App\Models\Sponsor\SponsorMeal;
use App\Services\File\FileService;
use Exception;
use Illuminate\Http\Request;

class SponsorController extends Controller
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
        $sponsors = Sponsor::where(['conference_id' => $conference->id, 'status' => 1])->orderBy('id', 'DESC')->get();
        return view('backend.sponsor.sponsor.index', compact('sponsors', 'society', 'conference'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($society, $conference)
    {
        // $societyDetail = society_detail();
        // if (is_super_admin() && empty($societyDetail)) {
        //     return redirect()->route('dashboard');
        // }
        // if (is_society_admin()) {
        //     $categories = SponsorCategory::where([
        //         'society_id' => current_user()->societies->value('id'),
        //         'status' => 1
        //     ])->latest()->get();
        // } elseif (is_super_admin()) {
        //     $categories = SponsorCategory::where([
        //         'society_id' => $societyDetail->id,
        //         'status' => 1
        //     ])->latest()->get();
        // } else {
        //     return redirect()->route('dashboard');
        // }
        $categories = SponsorCategory::where([
            'society_id' => $society->id,
            'status' => 1
        ])->latest()->get();
        return view('backend.sponsor.sponsor.create', compact('categories', 'society', 'conference'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $society, $conference)
    {
        try {
            $validated = $request->validate([
                'sponsor_category_id' => 'required',
                'name' => 'required',
                'amount' => 'required',
                'logo' => 'nullable|mimes:png,jpg',
                'flyers_ads' => 'nullable|mimes:png,jpg,pdf',
                'email' => 'nullable|email',
                'address' => 'nullable',
                'contact_person' => 'nullable',
                'phone' => 'required',
                'description' => 'nullable',
                'lunch_access' => 'required',
                'dinner_access' => 'required',
            ]);

            if (!empty($validated['logo'])) {
                $validated['logo'] = $this->file_service->fileUpload($validated['logo'], 'sponsor_logo', 'sponsor/logo');
                $validated['flyers_ads'] = $this->file_service->fileUpload($validated['flyers_ads'], 'sponsor_flyers_ads', 'sponsor/ads');
            }
            $validated['token'] = random_word(60);
            $validated['total_attendee'] = 1;

            $validated['conference_id'] = $conference->id;
            Sponsor::create($validated);

            return redirect()->route('sponsor.index', [$society, $conference])->with('status', 'Sponsor Added Successfully');
        } catch (Exception $e) {
            throw $e;
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
    public function edit($society, $conference, Sponsor $sponsor)
    {
        // $societyDetail = society_detail();
        // if (is_super_admin() && empty($societyDetail)) {
        //     return redirect()->route('dashboard');
        // }
        // if (is_society_admin()) {
        //     $categories = SponsorCategory::where([
        //         'society_id' => current_user()->societies->value('id'),
        //         'status' => 1
        //     ])->latest()->get();
        // } elseif (is_super_admin()) {
        //     $categories = SponsorCategory::where([
        //         'society_id' => $societyDetail->id,
        //         'status' => 1
        //     ])->latest()->get();
        // } else {
        //     return redirect()->route('dashboard');
        // }
        $categories = SponsorCategory::where([
            'society_id' => $society->id,
            'status' => 1
        ])->latest()->get();
        return view('backend.sponsor.sponsor.create', compact('sponsor', 'categories', 'society', 'conference'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $society, $conference, Sponsor $sponsor)
    {
        try {
            $validated = $request->validate([
                'sponsor_category_id' => 'required',
                'name' => 'required',
                'amount' => 'required',
                'logo' => 'nullable|mimes:png,jpg',
                'flyers_ads' => 'nullable|mimes:png,jpg,pdf',
                'email' => 'required|email',
                'address' => 'required',
                'contact_person' => 'nullable',
                'phone' => 'required',
                'description' => 'nullable',
                'lunch_access' => 'required',
                'dinner_access' => 'required',
            ]);

            if (!empty($validated['logo'])) {
                $this->file_service->deleteFile($sponsor->logo, 'sponsor/logo');
                //file upload function parameter required file,name,location
                $validated['logo'] = $this->file_service->fileUpload($validated['logo'], 'logo', 'sponsor/logo');
            }
            if (!empty($validated['flyers_ads'])) {
                $this->file_service->deleteFile($sponsor->flyers_ads, 'sponsor/ads');
                //file upload function parameter required file,name,location
                $validated['flyers_ads'] = $this->file_service->fileUpload($validated['flyers_ads'], 'sponsor_flyers_ads', 'sponsor/ads');
            }

            $sponsor->update($validated);

            return redirect()->route('sponsor.index', [$society, $conference])->with('status', 'Sponsor Updated Successfully');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($society, $conference, Sponsor $sponsor)
    {
        $sponsor->update(['status' => 0]);

        return redirect()->route('sponsor.index', [$society, $conference])->with('delete', 'Sponsor Deleted Successfully');
    }

    public function changeStatus(Sponsor $sponsor)
    {
        if ($sponsor->visible_status == 1) {
            $status = 0;
        } else {
            $status = 1;
        }

        $sponsor->update(['visible_status' => $status]);

        return redirect()->back()->with('status', 'Sponsor publish status changed successfully.');
    }



    public function addParticipant(Request $request)
    {
        $sponsor = Sponsor::whereId($request->id)->first();
        return view('backend.sponsor.sponsor.add-participant', compact('sponsor'));
    }

    public function addParticipantSubmit(Request $request)
    {
        try {
            $validateRequest = $request->validate([
                'additional_value' => 'required|numeric',
            ]);

            $sponsor = Sponsor::whereId($request->id)->first();

            $validated['total_attendee'] = $sponsor->total_attendee + $validateRequest['additional_value'];


            $sponsor->update($validated);

            $type = 'success';
            $message = "Total attendee edited successfully.";
        } catch (Exception $e) {
            $type = 'error';
            $message = $e->getMessage();
        }
        return response()->json(['type' => $type, 'message' => $message]);
    }

    public function generatePass($society, $conference)
    {
        $sponsors = Sponsor::where(['conference_id' => $conference->id, 'status' => 1])->orderBy('name', 'ASC')->get();
        $passSetting = PassSetting::where(['conference_id' => $conference->id, 'status' => 1])->first();

        return view('backend.sponsor.sponsor.pass', compact('sponsors', 'passSetting'));
    }

    public function sponsorProfile($token)
    {
        $sponsor = Sponsor::where('token', $token)->first();
        $checkAttendance = $sponsor
            ->attendances()
            ->where(['sponsor_id' => $sponsor->id, 'status' => 1])
            ->whereDate('created_at', date('Y-m-d'))
            ->first();
        // dd($checkAttendance);
        $totalLunchRemaining = $sponsor->total_attendee;
        $totalDinnerRemaining = $sponsor->total_attendee;
        $checkMeal = $sponsor
            ->meals()
            ->where(['sponsor_id' => $sponsor->id])
            ->whereDate('created_at', date('Y-m-d'))
            ->first();
        if (!empty($checkMeal)) {
            $totalLunchRemaining = $sponsor->total_attendee - $checkMeal->lunch_taken;
            $totalDinnerRemaining = $sponsor->total_attendee - $checkMeal->dinner_taken;
        }
        $passSetting = PassSetting::where('conference_id', $sponsor->conference_id)->first();
        return view('backend.sponsor.sponsor.sponsor-profile',  compact('sponsor', 'checkAttendance', 'totalLunchRemaining', 'totalDinnerRemaining',  'passSetting'));
    }

    public function takeAttendance(Request $request)
    {
        try {
            $data['sponsor_id'] = $request->sponsor_id;
            SponsorAttendance::create($data);
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            throw $e;
        }
    }


    public function takeMeal(Request $request)
    {
        try {
            $currentTime = date('H:i:s');
            $today = date('Y-m-d');

            $sponsor = Sponsor::find($request->sponsor_id);

            if (!$sponsor) {
                return response()->json(['success' => false, 'message' => 'sponsor not found.'], 404);
            }

            $passSetting = PassSetting::where('conference_id', $sponsor->conference_id)->first();

            if (!$passSetting) {
                return response()->json(['success' => false, 'message' => 'Meal settings not found.'], 404);
            }

            $isLunch = ($currentTime >= $passSetting->lunch_start_time && $currentTime <= $passSetting->lunch_end_time);
            $isDinner = ($currentTime >= $passSetting->dinner_start_time && $currentTime <= $passSetting->dinner_end_time);

            if (!$isLunch && !$isDinner) {
                return response()->json(['success' => false, 'message' => 'Meal is not available at this time.'], 403);
            }

            $mealRecord = SponsorMeal::where('sponsor_id', $request->sponsor_id)
                ->whereDate('created_at', $today)
                ->first();

            if (!$mealRecord) {
                // First meal record of the day
                $mealData = [
                    'sponsor_id' => $request->sponsor_id,
                    'lunch_taken' => $isLunch ? 1 : 0,
                    'dinner_taken' => $isDinner ? 1 : 0,
                ];

                SponsorMeal::create($mealData);
                $remaining = $sponsor->total_attendee - 1;
            } else {
                // Update existing record
                if ($isLunch) {
                    $mealRecord->lunch_taken += 1;
                    $remaining = $sponsor->total_attendee - $mealRecord->lunch_taken;
                } else {
                    $mealRecord->dinner_taken += 1;
                    $remaining = $sponsor->total_attendee - $mealRecord->dinner_taken;
                }

                $mealRecord->save();
            }

            return response()->json([
                'success' => true,
                'remaining' => $remaining
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
