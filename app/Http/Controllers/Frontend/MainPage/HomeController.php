<?php

namespace App\Http\Controllers\Frontend\MainPage;

use App\Http\Controllers\Controller;
use App\Models\Cms\Blog;
use App\Models\Cms\Feature;
use App\Models\Cms\Testimonial;
use App\Models\Cms\WhyChooseUs;
use App\Models\Conference\Conference;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class HomeController extends Controller
{
    private const CACHE_TTL = 3600;

    public function __construct(
        private Conference $conference,
        private Testimonial $testimonial,
        private WhyChooseUs $whyChooseUs,
        private Blog $blog,
        private Feature $feature
    ) {}

    public function index(): View
    {
        try {
            return view('frontend.main-page.home.index', [
                'conferences' => $this->getConferences(),
                'testimonials' => $this->getTestimonials(),
                'whyChooseUs' => $this->getWhyChooseUs(),
                'blogs' => $this->getBlogs(),
                'features' => $this->getFeatures()
            ]);
        } catch (Throwable $e) {
            Log::error('Error in HomeController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('frontend.main-page.home.index', [
                'conferences' => collect(),
                'testimonials' => collect(),
                'whyChooseUs' => collect(),
                'blogs' => collect(),
                'features' => collect()
            ]);
        }
    }

    private function getConferences()
    {
        $cacheKey = sprintf('home.conferences.%s', app()->environment());

        try {
            return Cache::remember($cacheKey, self::CACHE_TTL, function () {
                return $this->conference
                    ->with(['ConferenceVenueDetail', 'society'])
                    ->where('status', 1)
                    ->whereDate('start_date', '>=', Carbon::now())
                    ->orderBy('start_date', 'asc')
                    ->limit(3)
                    ->get();
            });
        } catch (\Throwable $e) {
            Log::warning('Cache failed for home.conferences; falling back to DB query', [
                'error' => $e->getMessage()
            ]);

            return $this->conference
                ->with(['ConferenceVenueDetail', 'society'])
                ->where('status', 1)
                ->whereDate('start_date', '>=', Carbon::now())
                ->orderBy('start_date', 'asc')
                ->limit(3)
                ->get();
        }
    }

    private function getTestimonials()
    {
        return Cache::remember('home.testimonials', self::CACHE_TTL, function () {
            return $this->testimonial->whereStatus(1)->get();
        });
    }

    private function getWhyChooseUs()
    {
        return Cache::remember('home.whyChooseUs', self::CACHE_TTL, function () {
            return $this->whyChooseUs->whereStatus(1)->get();
        });
    }

    private function getBlogs()
    {
        return Cache::remember('home.blogs', self::CACHE_TTL, function () {
            return $this->blog->whereStatus(1)->limit(3)->get();
        });
    }

    private function getFeatures()
    {
        return Cache::remember('home.features', self::CACHE_TTL, function () {
            return $this->feature->whereStatus(1)->get();
        });
    }
}
