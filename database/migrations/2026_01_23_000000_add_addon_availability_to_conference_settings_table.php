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
        Schema::table('conference_settings', function (Blueprint $table) {
            $table->enum('addon_availability', ['both', 'participant_only', 'accompany_only'])
                ->default('both')
                ->after('show_stats_dashboard')
                ->comment('Controls who can select add-ons: both, participant_only, or accompany_only');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conference_settings', function (Blueprint $table) {
            $table->dropColumn('addon_availability');
        });
    }
};
