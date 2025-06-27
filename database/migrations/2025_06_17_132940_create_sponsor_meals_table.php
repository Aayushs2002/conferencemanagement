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
        Schema::create('sponsor_meals', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sponsor_id')->nullable();
            $table->tinyInteger('lunch_taken')->nullable();
            $table->tinyInteger('dinner_taken')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsor_meals');
    }
};
