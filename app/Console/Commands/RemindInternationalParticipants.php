<?php

namespace App\Console\Commands;

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
                $query->where('country_id', '!=', 125); 
            })
            ->whereDoesntHave('internationalAccommodation')
            ->whereHas('conference', function ($query) {
                $query->whereDate('start_date', '>', Carbon::now())
                    ->where('status', 1);
            })
            ->with(['user.userDetail', 'conference'])
            ->get();

        $count = 0;
        foreach ($internationalRegistrations as $registration) {
            if ($registration->conference) {
                try {
                    $registration->user->notify(new AccommodationDetailReminder($registration->conference));
                    $count++;
                } catch (\Exception $e) {
                    $this->error("Failed to send notification for registration ID {$registration->id}: {$e->getMessage()}");
                }
            }
        }

        $this->info("Successfully sent reminders to {$count} international participants.");
    }
}
