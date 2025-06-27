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
        Schema::create('conference_venue_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('conference_id')->nullable();
            $table->string('venue_name')->nullable();
            $table->string('venue_address')->nullable();
            $table->string('venue_contact_person_name')->nullable();
            $table->string('venue_phone_number')->nullable();
            $table->string('venue_email')->nullable();
            $table->longText('google_map_link')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conference_venue_details');
    }
};
