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
        Schema::table('pass_settings', function (Blueprint $table) {
            $table->string('workshop_participant_name_tag')->nullable()->after('dinner_end_time');
            $table->string('workshop_participant_color')->nullable()->after('workshop_participant_name_tag');
            $table->string('workshop_trainer_name_tag')->nullable()->after('workshop_participant_color');
            $table->string('workshop_trainer_color')->nullable()->after('workshop_trainer_name_tag');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pass_settings', function (Blueprint $table) {
            $table->dropColumn([
                'workshop_participant_name_tag',
                'workshop_participant_color',
                'workshop_trainer_name_tag',
                'workshop_trainer_color'
            ]);
        });
    }
};
