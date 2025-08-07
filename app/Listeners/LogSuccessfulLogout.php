<?php

namespace App\Listeners;

use App\Models\LoginHistory;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogSuccessfulLogout
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
    public function handle(Logout $event): void
    {
        $history = LoginHistory::where('user_id', $event->user->id)
            ->whereNull('logged_out_at')
            ->latest()
            ->first();

        if ($history) {
            $history->update([
                'logged_out_at' => now(),
            ]);
        }
    }
}
