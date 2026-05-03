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
        Schema::table('conference_certificates', function (Blueprint $table) {
            $table->boolean('show_presentation_type')->default(false)->after('include_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conference_certificates', function (Blueprint $table) {
            $table->dropColumn('show_presentation_type');
        });
    }
};
