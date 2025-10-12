<?php

namespace App\Commands;

use App\Models\Conference\ConferenceRegistration;
use App\Notifications\AccommodationDetailReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RemindInternationalParticipants extends Command
{
    protected $signature = 'participants:remind-accommodation';
    protected $description = 'Remind international participants to update their accommodation details';

    public function handle()
    {
        $internationalRegistrations = ConferenceRegistration::query()
            ->whereHas('user.userDetail', function ($query) {
                $query->where('country_id', '!=', 125); // Not Nepal
            })
            ->whereDoesntHave('internationalAccommodation')
            ->whereHas('conference', function ($query) {
                $query->whereDate('start_date', '>', Carbon::now())
                    ->where('status', 1);
            })
            ->with(['user', 'conference'])
            ->get();

        $count = 0;
        foreach ($internationalRegistrations as $registration) {
            $registration->user->notify(new AccommodationDetailReminder($registration->conference));
            $count++;
        }

        $this->info("Sent reminders to {$count} international participants.");
        
        return self::SUCCESS;
    }
}