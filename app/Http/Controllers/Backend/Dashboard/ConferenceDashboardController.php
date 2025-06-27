<?php

namespace App\Http\Controllers\Backend\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ConferenceDashboardController extends Controller
{
    public function registrationData(Request $request)
    {
        $range = $request->input( 'range', 'last_7_days');
       
        $labels = [];
        $dates = [];

        switch ($range) {
            case 'today':
                $start = Carbon::today();
                $end = Carbon::today()->endOfDay();
                $dates = [Carbon::today()->toDateString()];
                break;

            case 'yesterday':
                $start = Carbon::yesterday();
                $end = Carbon::yesterday()->endOfDay();
                $dates = [Carbon::yesterday()->toDateString()];
                break;

            case 'last_7_days':
                $start = Carbon::now()->subDays(6)->startOfDay();
                $end = Carbon::now()->endOfDay();
                $dates = collect(range(0, 6))->map(fn($i) => Carbon::now()->subDays(6 - $i)->toDateString());
                break;

            case 'last_30_days':
                $start = Carbon::now()->subDays(29)->startOfDay();
                $end = Carbon::now()->endOfDay();
                $dates = collect(range(0, 29))->map(fn($i) => Carbon::now()->subDays(29 - $i)->toDateString());
                break;

            case 'current_month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                $dates = collect(range(1, Carbon::now()->daysInMonth))->map(function ($d) {
                    return Carbon::createFromDate(null, null, $d)->toDateString();
                });
                break;

            case 'last_month':
                $start = Carbon::now()->subMonth()->startOfMonth();
                $end = Carbon::now()->subMonth()->endOfMonth();
                $dates = collect(range(1, $start->daysInMonth))->map(function ($d) use ($start) {
                    return Carbon::createFromDate($start->year, $start->month, $d)->toDateString();
                });
                break;

            default:
                $start = Carbon::now()->subDays(6)->startOfDay();
                $end = Carbon::now()->endOfDay();
                $dates = collect(range(0, 6))->map(fn($i) => Carbon::now()->subDays(6 - $i)->toDateString());
                break;
        }

        $registrations = DB::table('conference_registrations')
            ->select(DB::raw("DATE(created_at) as date"), DB::raw("COUNT(*) as count"))
            ->where('conference_id', $request->conference_id)
            ->where('status', 1)
            ->where('verified_status', 1)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy(DB::raw("DATE(created_at)"))
            ->pluck('count', 'date')
            ->all();

        $counts = [];
        $labels = [];

        foreach ($dates as $date) {
            $counts[] = $registrations[$date] ?? 0;
            $labels[] = Carbon::parse($date)->format(match ($range) {
                'last_7_days', 'today', 'yesterday' => 'D',
                'last_30_days', 'current_month', 'last_month' => 'd M',
                default => 'D'
            });
        }

        return response()->json([
            'labels' => $labels,
            'counts' => $counts,
        ]);
    }
}
