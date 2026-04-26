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
            $table->tinyInteger('include_signature')->default(1)->after('custom_css');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conference_certificates', function (Blueprint $table) {
            $table->dropColumn('include_signature');
        });
    }
};
