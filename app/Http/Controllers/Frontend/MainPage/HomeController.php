<?php

namespace App\Http\Controllers\Frontend\MainPage;

use App\Http\Controllers\Controller;
use App\Models\Cms\Blog;
use App\Models\Cms\Feature;
use App\Models\Cms\Testimonial;
use App\Models\Cms\WhyChooseUs;
use App\Models\Conference\Conference;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $conferences = Conference::with(['ConferenceVenueDetail', 'society'])->where('status', 1)
            ->whereDate('start_date', '>=', Carbon::now())
            ->orderBy('start_date', 'asc')
            ->limit(3)
            ->get();

        $testimonials = Testimonial::whereStatus(1)->get();
        $whyChooseUs = WhyChooseUs::whereStatus(1)->get();
        $blogs = Blog::whereStatus(1)->limit(3)->get();
        $features = Feature::whereStatus(1)->get();
        return view('frontend.main-page.home.index', compact('conferences', 'testimonials', 'whyChooseUs', 'blogs', 'features'));
    }
}
