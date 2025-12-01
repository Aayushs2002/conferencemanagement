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
        Schema::table('submission_settings', function (Blueprint $table) {
            $table->boolean('copy_paste_allowed')->default(true)->after('contribution_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submission_settings', function (Blueprint $table) {
            $table->dropColumn('copy_paste_allowed');
        });
    }
};
