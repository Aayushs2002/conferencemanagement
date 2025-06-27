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
        Schema::create('workshop_registrations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('workshop_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('payment_voucher')->nullable();
            $table->integer('payment_type')->nullable(); //1 for fone-pay, 2 for card payment, 3 for payment voucher
            $table->tinyInteger('verified_status')->default(0); // 0 for pending, 1 for accepted, 2 for rejected
            $table->string('token')->nullable();
            $table->longText('remarks')->nullable();
            $table->string('amount')->nullable();
            $table->tinyInteger('meal_type')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_registrations');
    }
};
