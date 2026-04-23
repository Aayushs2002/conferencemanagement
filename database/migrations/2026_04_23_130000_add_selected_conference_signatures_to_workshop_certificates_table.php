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
        Schema::table('workshop_certificates', function (Blueprint $table) {
            $table->json('selected_conference_signatures')->nullable()->after('include_conference_signatures');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshop_certificates', function (Blueprint $table) {
            $table->dropColumn('selected_conference_signatures');
        });
    }
};
