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
        Schema::create('workshop_chair_person_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('workshop_id')->nullable();
            $table->bigInteger('chairperson_id')->nullable();
            $table->string('photo')->nullable();
            $table->longText('short_cv')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_chair_person_details');
    }
};
