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
        Schema::create('scientific_sessions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('conference_id')->nullable();
            $table->string('day')->nullable();
            $table->bigInteger('hall_id')->nullable();
            $table->bigInteger('scientific_session_category_id')->nullable();
            $table->bigInteger('session_chair_id')->nullable();
            $table->bigInteger('presenter_id')->nullable();
            $table->string('topic')->nullable();
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->longText('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scientific_sessions');
    }
};
