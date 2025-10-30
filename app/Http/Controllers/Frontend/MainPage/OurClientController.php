<?php

namespace App\Http\Controllers\Frontend\MainPage;

use App\Http\Controllers\Controller;
use App\Http\Requests\SocietyDetailRequest;
use App\Models\User\Society;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class OurClientController extends Controller
{
    private const CACHE_TTL = 3600;
    private const CONFERENCES_PER_PAGE = 3;

    public function __construct(
        private readonly Society $society
    ) {} 

    public function index(): View
    {
        try {
            $societies = Cache::remember('societies.active', self::CACHE_TTL, function () {
                return $this->society
                    ->where('status', 1)
                    ->with(['users' => function ($query) {
                        $query->select('users.id', 'users.f_name');
                    }])
                    ->get();
            });

            return view('frontend.main-page.our-client.index', compact('societies'));
        } catch (Throwable $e) {
            // dd($e);
            Log::error('Error fetching societies for index page', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('frontend.main-page.our-client.index', ['societies' => collect()]);
        }
    }

    public function detail(SocietyDetailRequest $request, string $slug): View
    {
        try {
            $society = $this->getSociety($slug);
            // dd($society);
            return view('frontend.main-page.our-client.detail', [
                'society' => $society,
                'currentConferences' => $this->getCurrentConferences($society),
                'previousConferences' => $this->getPreviousConferences($society),
                'activeTab' => $this->determineActiveTab($request)
            ]);
        } catch (Throwable $e) {
            // dd($e);
            Log::error('Error in society detail page', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'society_slug' => $slug
            ]);

            abort(404);
        }
    }

    private function getSociety(string $slug): Society
    {
        return Cache::remember(
            "society.{$slug}", 
            self::CACHE_TTL, 
            fn() => $this->society->where(['slug' => $slug, 'status' => 1])->firstOrFail()
        );
    }

    private function getCurrentConferences(Society $society)
    {
        $cacheKey = "society.{$society->id}.current_conferences." . request()->get('current_page', 1);
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($society) {
            return $this->getConferenceQuery($society)
                ->whereDate('end_date', '>=', Carbon::today())
                ->orderBy('start_date', 'asc')
                ->paginate(self::CONFERENCES_PER_PAGE, ['*'], 'current_page');
        });
    }

    private function getPreviousConferences(Society $society)
    {
        $cacheKey = "society.{$society->id}.previous_conferences." . request()->get('previous_page', 1);
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($society) {
            return $this->getConferenceQuery($society)
                ->whereDate('end_date', '<', Carbon::today())
                ->orderBy('start_date', 'desc')
                ->paginate(self::CONFERENCES_PER_PAGE, ['*'], 'previous_page');
        });
    }

    private function getConferenceQuery(Society $society): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $society->conferences()
            ->with(['ConferenceVenueDetail'])
            ->where('status', 1);
    }

    private function determineActiveTab(SocietyDetailRequest $request): string
    {
        return $request->has('previous_page') ? 'previous' : 'current';
    }
}
