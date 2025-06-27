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
            $table->string('moco_merchant_id')->nullable();
            $table->string('moco_outlet_id')->nullable();
            $table->string('moco_terminal_id')->nullable();
            $table->string('moco_shared_key')->nullable();
            $table->longText('account_detail')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('national_payments', function (Blueprint $table) {
            $table->dropColumn([
                'moco_merchant_id',
                'moco_outlet_id',
                'moco_terminal_id',
                'moco_shared_key',
                'account_detail',
            ]);
        });
    }
};
