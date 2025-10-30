<?php

namespace App\Http\Controllers\Frontend\Conference;

use App\Models\Faq\Faq;
use App\Models\Sponsor\SponsorCategory;
use App\Models\Conference\ConferenceRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

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

        $cacheKey = "conference_stats_{$this->conference->id}";
        $stats = Cache::remember($cacheKey, 300, function () {
            return ConferenceRegistration::getConferenceStats($this->conference->id);
        });

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
 
        $faqs = Faq::where(['conference_id' => $this->conference->id, 'status' => 1])->get();

        $society = $this->conference->society;
        $subdomain = $society->sub_domain_name;
        $host = request()->getHost();

        if (!str_starts_with($host, $subdomain . '.')) {
            $mainDomain = preg_replace('/^www\./', '', $host);
            $url = "http://{$subdomain}.{$mainDomain}" . request()->getRequestUri();
            return redirect()->to($url);
        }
        return view('frontend.conference.home.index', compact('submissionSetting', 'hotels', 'sponsorCategories', 'downloads', 'memberTypes', 'faqs', 'stats'));
    }
}
