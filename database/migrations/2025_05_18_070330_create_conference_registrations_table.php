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
        Schema::create('conference_registrations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('conference_id')->nullable();
            $table->tinyInteger('registrant_type')->nullable(); // 1 for attendee, 2 for presenter, 3 for session chair, 4 for special guest,5 for support team
            $table->tinyInteger('attend_type')->default(1); // 1 for physical, 2 for online
            $table->tinyInteger('payment_type')->nullable(); //1 for fone-pay, 2 for card payment, 3 for payment voucher
            $table->string('payment_voucher')->nullable();
            $table->string('amount')->nullable();
            $table->string('transaction_id')->nullable();
            $table->tinyInteger('verified_status')->default(0); // 0 for pending, 1 for accepted, 2 for rejected
            $table->string('token')->nullable();
            $table->tinyInteger('total_attendee')->nullable();
            $table->tinyInteger('is_invited')->default(0); //1 for invitee, 0 for non invite
            $table->boolean('is_featured')->default(0);
            $table->longText('remarks')->nullable();
            $table->longText('short_cv')->nullable();
            $table->tinyInteger('meal_type')->nullable();
            $table->string('registration_id')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conference_registrations');
    }
};
