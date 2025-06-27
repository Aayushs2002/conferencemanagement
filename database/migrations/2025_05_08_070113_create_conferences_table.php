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
        Schema::create('conferences', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('society_id')->nullable();
            $table->string('conference_name')->nullable();
            $table->string('conference_theme')->nullable();
            $table->string('conference_logo')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('regular_registration_deadline')->nullable();
            $table->date('early_bird_registration_deadline')->nullable();
            $table->longText('conference_description')->nullable();
            $table->longText('primary_color')->nullable();
            $table->longText('secendary_color')->nullable();
            $table->string('slug')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conferences');
    }
};
