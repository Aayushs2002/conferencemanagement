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
        Schema::table('conference_user_pass_designations', function (Blueprint $table) {
            $table->string('color', 7)->nullable()->after('pass_designation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conference_user_pass_designations', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
