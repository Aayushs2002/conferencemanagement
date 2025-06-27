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
        Schema::table('workshop_trainers', function (Blueprint $table) {
            $table->dropColumn(['name', 'email', 'image', 'affiliation', 'cv']);
            $table->bigInteger('trainer_id')->nullable()->after('workshop_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshop_trainers', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('image')->nullable();
            $table->string('affiliation')->nullable();
            $table->string('cv')->nullable();
            $table->dropColumn('trainer_id');
        });
    }
};
