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
            $table->string('registration_guideline_youtube')->nullable()->after('registration_guideline');
            $table->string('submission_guideline_youtube')->nullable()->after('registration_guideline_youtube');
            $table->string('expert_guideline_youtube')->nullable()->after('submission_guideline_youtube');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conference_settings', function (Blueprint $table) {
            $table->dropColumn(['registration_guideline_youtube', 'submission_guideline_youtube', 'expert_guideline_youtube']);
        });
    }
};
