<?php

namespace App\Http\Controllers\Frontend\MainPage;

use App\Http\Controllers\Controller;
use App\Models\User\Society;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OurClientController extends Controller
{
    public function index()
    {
        return view('frontend.main-page.our-client.index');
    }

    public function detail(Request $request, $slug)
    {
        $society = Society::where(['slug' => $slug, 'status' => 1])->firstOrFail();
        $today = Carbon::today();

        $activeTab = $request->has('previous_page') ? 'previous' : 'current';

        $currentConferences = $society->conferences()
            ->whereDate('end_date', '>=', $today)
            ->orderBy('start_date', 'asc')
            ->paginate(3, ['*'], 'current_page');

        $previousConferences = $society->conferences()
            ->whereDate('end_date', '<', $today)
            ->orderBy('start_date', 'desc')
            ->paginate(3, ['*'], 'previous_page');

        return view('frontend.main-page.our-client.detail', compact(
            'society',
            'currentConferences',
            'previousConferences',
            'activeTab'
        ));
    }
}
