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
        Schema::table('national_payments', function (Blueprint $table) {
            $table->string('connectips_merchant_id')->nullable()->after('khalti_live_secret_key');
            $table->string('connectips_app_id')->nullable()->after('connectips_merchant_id');
            $table->string('connectips_app_name')->nullable()->after('connectips_app_id');
            $table->string('connectips_password')->nullable()->after('connectips_app_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('national_payments', function (Blueprint $table) {
            $table->dropColumn(['connectips_merchant_id', 'connectips_app_id', 'connectips_app_name', 'connectips_password']);
        });
    }
};
