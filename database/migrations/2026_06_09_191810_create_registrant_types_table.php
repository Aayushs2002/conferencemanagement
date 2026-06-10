<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('registrant_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('conference_id')->nullable()->index();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        // Seed the 8 default global types matching existing integer values in conference_registrations
        \Illuminate\Support\Facades\DB::table('registrant_types')->insert([
            ['id' => 1, 'name' => 'Attendee',     'conference_id' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Speaker',       'conference_id' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Session Chair', 'conference_id' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Special Guest', 'conference_id' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'Organizer',     'conference_id' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'name' => 'Faculty',       'conference_id' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'name' => 'Volunteer',     'conference_id' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'name' => 'Invitee',       'conference_id' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Advance the PostgreSQL sequence past the seeded IDs so new inserts don't conflict
        \Illuminate\Support\Facades\DB::statement("SELECT setval('registrant_types_id_seq', (SELECT MAX(id) FROM registrant_types))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrant_types');
    }
};
