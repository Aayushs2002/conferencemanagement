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
        Schema::create('sponsors', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('conference_id')->nullable();
            $table->bigInteger('sponsor_category_id')->nullable();
            $table->string('name')->nullable();
            $table->string('amount')->nullable();
            $table->string('logo')->nullable();
            $table->string('flyers_ads')->nullable();
            $table->string('address')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->longText('description')->nullable();
            $table->integer('total_attendee')->nullable();
            $table->tinyInteger('visible_status')->default(1);
            $table->tinyInteger('lunch_access')->default(1);
            $table->tinyInteger('dinner_access')->default(1);
            $table->string('token')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsors');
    }
};
