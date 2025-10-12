<?php

namespace Database\Seeders;

use App\Models\Conference\Conference;
use App\Models\Conference\ConferenceRegistration;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Seeder;

class TestInternationalParticipantSeeder extends Seeder
{
    public function run()
    {
        // Create test users with different countries
        $internationalUsers = [
            [
                'name' => 'John Doe',
                'email' => 'john@test.com',
                'country_id' => 231, // USA
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@test.com',
                'country_id' => 99, // India
            ],
            [
                'name' => 'Local User',
                'email' => 'local@test.com',
                'country_id' => 125, // Nepal
            ]
        ];

        foreach ($internationalUsers as $userData) {
            $user = User::create([
                'email' => $userData['email'],
                'password' => bcrypt('password123'),
                'status' => 1,
            ]);

            UserDetail::create([
                'user_id' => $user->id,
                'f_name' => $userData['name'],
                'country_id' => $userData['country_id'],
                'status' => 1,
            ]);

            // Create conference registration for each user
            if ($userData['country_id'] != 125) { // Only for international users
                ConferenceRegistration::create([
                    'user_id' => $user->id,
                    'conference_id' => Conference::where('status', 1)->first()->id,
                    'registrant_type' => 1,
                    'status' => 1,
                    'verified_status' => 1
                ]);
            }
        }
    }
}