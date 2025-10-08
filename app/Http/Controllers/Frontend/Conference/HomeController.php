<?php

namespace App\Http\Controllers\Frontend\Conference;

use App\Models\Faq\Faq;
use App\Models\Sponsor\SponsorCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends BaseConferenceController
{
    public function index()
    {
        // dd($this->conference);  
        $submissionSetting = $this->conference->submissionSetting;
        $hotels = $this->conference->hotels;
        $sponsorCategories = SponsorCategory::where([
            'status' => 1,
            'society_id' => $this->conference->society_id
        ])
            ->orderBy('id', 'ASC')
            ->with(['sponsors' => function ($query) {
                $query->where('conference_id', $this->conference->id);
            }])
            ->get();
        $downloads = $this->conference->downloads;

        $condition = "WHERE conference_id = " . $this->conference->id;
        $sql = "SELECT
                    MT.id,
                    MT.type, 
                    MT.delegate,
                    MTP.price_id,
                    MTP.conference_id,
                    MTP.member_type_id,
                    MTP.early_bird_amount,
                    MTP.regular_amount,
                    MTP.on_site_amount,
                    MTP.guest_amount
                FROM member_types AS MT
                LEFT JOIN
                    (SELECT
                        id AS price_id,
                        conference_id,
                        member_type_id,
                        early_bird_amount,
                        regular_amount,
                        on_site_amount,
                        guest_amount
                    FROM
                        conference_member_type_prices
                        $condition
                    ) AS MTP ON MT.id = MTP.member_type_id
                    WHERE MT.society_id = " . $this->conference->society_id;

        $memberTypes = DB::select($sql);
        // dd($memberTypes);
        $faqs = Faq::where(['conference_id' => $this->conference->id, 'status' => 1])->get();
        return view('frontend.conference.home.index', compact('submissionSetting', 'hotels', 'sponsorCategories', 'downloads', 'memberTypes', 'faqs'));
    }
}
