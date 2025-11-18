<?php

namespace App\Http\Controllers\Frontend\MainPage;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConferenceFilterRequest;
use App\Models\Conference\Conference;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class ConferenceController extends Controller
{
    private const CACHE_TTL = 3600; 

    public function __construct( 
        private readonly Conference $conference
    ) {}

    public function index(ConferenceFilterRequest $request): View
    {
        try {
            $conferences = $this->getFilteredConferences($request);
            return view('frontend.main-page.conference.index', compact('conferences'));
        } catch (Throwable $e) {
            Log::error('Error fetching conferences', [
                'error' => $e->getMessage(), 
                'trace' => $e->getTraceAsString(),
                'filters' => $request->validated()
            ]);

            return view('frontend.main-page.conference.index', ['conferences' => collect()]);
        }
    }

    public function filter(ConferenceFilterRequest $request): string
    {
        try {
            $conferences = $this->getFilteredConferences($request);
            return view('frontend.main-page.conference.partials.conference-cards', 
                compact('conferences')
            )->render();
        } catch (Throwable $e) {
            Log::error('Error filtering conferences', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'filters' => $request->validated()
            ]);

            return view('frontend.main-page.conference.partials.conference-cards', 
                ['conferences' => collect()]
            )->render();
        }
    }

    private function getFilteredConferences(ConferenceFilterRequest $request)
    {
        $cacheKey = $this->generateCacheKey($request); 

        // return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($request) {
            $query = $this->conference
                ->with(['society', 'ConferenceVenueDetail'])
                ->where('status', 1);

            $this->applyFilters($query, $request);

            return $query->orderBy('start_date', 'desc')->get();
        // });
    }

    private function applyFilters($query, ConferenceFilterRequest $request): void
    {
        if ($request->filled('search')) {
            $query->where('conference_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('organization')) { 
            $query->where('society_id', $request->organization);
        }

        if ($request->filled('tags')) {
            $query->where('tags', 'like', '%' . $request->tags . '%');
        }

        if ($request->filled('month')) {
            $monthNumber = date('m', strtotime($request->month . ' 1'));
            $query->whereMonth('start_date', $monthNumber);
        }
    }

    private function generateCacheKey(ConferenceFilterRequest $request): string
    {
        $filters = $request->validated();
        ksort($filters); 
        return 'conferences.filter.' . md5(json_encode($filters));
    }
}
