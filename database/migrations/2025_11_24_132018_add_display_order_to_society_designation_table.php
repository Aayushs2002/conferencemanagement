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
        Schema::table('society_designation', function (Blueprint $table) {
            $table->integer('display_order')->default(0)->after('designation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('society_designation', function (Blueprint $table) {
            $table->dropColumn('display_order');
        });
    }
};
