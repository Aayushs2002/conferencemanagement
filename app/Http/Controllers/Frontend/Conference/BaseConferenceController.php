<?php

namespace App\Http\Controllers\Frontend\Conference;

use App\Http\Controllers\Controller;
use App\Models\Conference\Conference;
use App\Models\Workshop\Workshop;
use Carbon\Carbon;
use Illuminate\Support\Collection; 

class BaseConferenceController extends Controller
{
    protected ?Conference $conference = null;
    protected Collection $workshops;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {

            $slug = $request->route('conference_front');
            $this->conference = Conference::with(['society', 'conferenceVenueDetail', 'conferenceSetting'])
                ->where('slug', $slug)
                ->firstOrFail();

            $portalAccessEndAt = $this->conference->conferenceSetting?->portal_access_end_at
                ? Carbon::parse($this->conference->conferenceSetting->portal_access_end_at)
                : Carbon::parse($this->conference->end_date)->endOfDay();

            if (now()->greaterThan($portalAccessEndAt)) {
                return redirect()
                    ->back()
                    ->with('poperror', 'This conference portal is closed.');
            }

            $this->workshops = Workshop::where('conference_id', $this->conference->id)
                ->where('approval_status', 'approved')
                ->where('status', 1)
                ->where('is_published', true)
                ->orderBy('display_order', 'ASC')
                ->get();

            view()->share([
                'conference' => $this->conference,
                'workshops'  => $this->workshops,
            ]);

            return $next($request);
        });
    }
}
