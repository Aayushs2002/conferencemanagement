<?php

namespace App\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class TestAccommodationReminder extends Command
{
    protected $signature = 'test:accommodation-reminder';
    protected $description = 'Test the accommodation reminder system';

    public function handle()
    {
        $this->info('Starting accommodation reminder test...');

        // Run the seeder
        $this->info('Seeding test data...');
        Artisan::call('db:seed', [
            '--class' => 'TestInternationalParticipantSeeder'
        ]);

        // Run the reminder command
        $this->info('Running reminder command...');
        Artisan::call('participants:remind-accommodation');

        // Show the output
        $this->info(Artisan::output());

        // Clean up test data if needed
        // Uncomment if you want to remove test data after running
        // $this->info('Cleaning up test data...');
        // User::whereIn('email', ['john@test.com', 'jane@test.com', 'local@test.com'])->delete();

        $this->info('Test completed!');
        
        return self::SUCCESS;
    }
}