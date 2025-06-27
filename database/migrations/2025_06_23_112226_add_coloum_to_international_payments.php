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
        Schema::table('international_payments', function (Blueprint $table) {
            $table->longText('access_token')->nullable();
            $table->longText('merchant_signing_private_key')->nullable();
            $table->longText('paco_encryption_public_key')->nullable();
            $table->longText('merchant_decryption_private_key')->nullable();
            $table->longText('paco_signing_public_key')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('international_payments', function (Blueprint $table) {
            //
        });
    }
};
