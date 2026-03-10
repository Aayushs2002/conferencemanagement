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
            $table->string('payment_type')->nullable()->after('status');
            $table->longText('qr_details')->nullable()->after('payment_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('international_payments', function (Blueprint $table) {
            $table->dropColumn(['payment_type', 'qr_details']);
        });
    }
};
