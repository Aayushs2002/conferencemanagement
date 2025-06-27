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
        Schema::table('conference_user_permission', function (Blueprint $table) {
            $table->unique(['conference_id', 'user_id', 'permission_id'], 'c_u_p_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conference_user_permission', function (Blueprint $table) {
             $table->dropUnique('c_u_p_unique');
        });
    }
};
