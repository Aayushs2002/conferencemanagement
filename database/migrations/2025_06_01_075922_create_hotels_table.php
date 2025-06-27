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
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('conference_id')->nullable();
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('images')->nullable();
            $table->smallInteger('rating')->nullable();
            $table->text('google_map')->nullable();
            $table->longText('description')->nullable();
            $table->string('price')->nullable();
            $table->string('website')->nullable();
            $table->longText('facility')->nullable();
            $table->tinyInteger('visible_status')->default(1);
            $table->string('promo_code')->nullable();
            $table->string('contact_person')->nullable();
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
        Schema::dropIfExists('hotels');
    }
};
