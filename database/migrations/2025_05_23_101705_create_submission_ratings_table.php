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
        Schema::create('submission_ratings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('submission_id')->nullable();
            $table->integer('introduction')->nullable();
            $table->integer('method')->nullable();
            $table->integer('result')->nullable();
            $table->integer('conclusion')->nullable();
            $table->integer('grammar')->nullable();
            $table->integer('overall_rating')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_ratings');
    }
};
