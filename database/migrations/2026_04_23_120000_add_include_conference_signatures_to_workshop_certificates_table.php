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
            $table->tinyInteger('include_conference_signatures')->default(1)->after('signature_designation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshop_certificates', function (Blueprint $table) {
            $table->dropColumn('include_conference_signatures');
        });
    }
};
