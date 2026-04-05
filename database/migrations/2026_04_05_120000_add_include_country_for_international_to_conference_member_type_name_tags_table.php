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
        Schema::table('pass_settings', function (Blueprint $table) {
            $table->boolean('include_country_for_international')->default(false)->after('workshop_trainer_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pass_settings', function (Blueprint $table) {
            $table->dropColumn('include_country_for_international');
        });
    }
};
