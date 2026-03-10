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
        Schema::table('conference_registrations', function (Blueprint $table) {
            $table->string('payment_currency', 10)->default('USD')->after('amount')->comment('Currency used for payment (USD or INR)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conference_registrations', function (Blueprint $table) {
            $table->dropColumn('payment_currency');
        });
    }
};
