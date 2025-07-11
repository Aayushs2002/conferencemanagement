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
        Schema::create('workshop_venue_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('workshop_id')->nullable();
            $table->string('venue_name');
            $table->text('venue_address');
            $table->string('google_map_link');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_venue_details');
    }
};
