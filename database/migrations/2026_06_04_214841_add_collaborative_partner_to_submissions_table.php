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
        Schema::table('submissions', function (Blueprint $table) {
            $table->string('collaborative_partner')->nullable()->after('status');
        });

        // Drop collaborative_partner_logos from submission_settings as it is not needed
        Schema::table('submission_settings', function (Blueprint $table) {
            $table->dropColumn('collaborative_partner_logos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn('collaborative_partner');
        });

        Schema::table('submission_settings', function (Blueprint $table) {
            $table->json('collaborative_partner_logos')->nullable();
        });
    }
};
