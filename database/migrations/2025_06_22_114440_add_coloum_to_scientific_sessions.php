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
        Schema::table('scientific_sessions', function (Blueprint $table) {
            $table->bigInteger('submission_id')->nullable();
            $table->tinyInteger('is_from_submission')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scientific_sessions', function (Blueprint $table) {
            //
        });
    }
};
