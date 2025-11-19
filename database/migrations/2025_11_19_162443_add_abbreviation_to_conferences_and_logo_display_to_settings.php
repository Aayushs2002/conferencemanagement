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
        Schema::table('conferences', function (Blueprint $table) {
            $table->string('abbreviation')->nullable()->after('conference_name');
        });

        Schema::table('conference_settings', function (Blueprint $table) {
            $table->enum('logo_display_type', ['logo', 'abbreviation'])->default('logo')->after('expert_guideline_youtube');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conferences', function (Blueprint $table) {
            $table->dropColumn('abbreviation');
        });

        Schema::table('conference_settings', function (Blueprint $table) {
            $table->dropColumn('logo_display_type');
        });
    }
};
