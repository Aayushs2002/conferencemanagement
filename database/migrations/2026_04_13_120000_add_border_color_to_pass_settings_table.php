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
            $table->string('border_color')->nullable()->after('dinner_end_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pass_settings', function (Blueprint $table) {
            $table->dropColumn('border_color');
        });
    }
};
