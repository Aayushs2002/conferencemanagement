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
        // Add column to track if specific add-ons are included for accompanying persons
        Schema::table('conference_registration_addons', function (Blueprint $table) {
            $table->boolean('include_for_guests')->default(false)->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conference_registration_addons', function (Blueprint $table) {
            $table->dropColumn('include_for_guests');
        });
    }
};
