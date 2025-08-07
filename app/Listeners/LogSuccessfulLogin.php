<?php

namespace App\Listeners;

use App\Models\LoginHistory;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Jenssegers\Agent\Agent;
use Stevebauman\Location\Facades\Location;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $ip = request()->ip();
        $location = Location::get($ip);
        $agent = new Agent();

        LoginHistory::create([
            'user_id'     => $event->user->id,
            'ip_address'  => $ip,
            'user_agent'  => request()->userAgent(),
            'browser'     => $agent->browser(),
            'os'          => $agent->platform(),
            'country'     => $location->countryName ?? null,
            'region'      => $location->regionName ?? null,
            'city'        => $location->cityName ?? null,
            'latitude'    => $location->latitude ?? null,
            'longitude'   => $location->longitude ?? null,
            'logged_in_at' => now(),
        ]);
    }
}
