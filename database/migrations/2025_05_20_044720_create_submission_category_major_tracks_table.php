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
        Schema::create('submission_category_major_tracks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('conference_id')->nullable();
            $table->string('title')->nullable();
            $table->longText('major_areas')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_category_major_tracks');
    }
};
