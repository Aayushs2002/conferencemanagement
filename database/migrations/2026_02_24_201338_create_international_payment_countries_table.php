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
        Schema::create('international_payment_countries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('international_payment_id');
            $table->unsignedBigInteger('country_id');
            $table->timestamps();

            $table->foreign('international_payment_id')->references('id')->on('international_payments')->onDelete('cascade');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            
            $table->unique(['international_payment_id', 'country_id'], 'payment_country_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('international_payment_countries');
    }
};
