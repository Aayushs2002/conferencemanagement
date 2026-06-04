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
        Schema::table('submission_settings', function (Blueprint $table) {
            $table->tinyInteger('show_collaborative_partner')->default(0)->after('copy_paste_allowed');
            $table->json('collaborative_partner_logos')->nullable()->after('show_collaborative_partner');
        });

        Schema::table('email_templates', function (Blueprint $table) {
            $table->json('partner_filter')->nullable()->after('body');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submission_settings', function (Blueprint $table) {
            $table->dropColumn(['show_collaborative_partner', 'collaborative_partner_logos']);
        });

        Schema::table('email_templates', function (Blueprint $table) {
            $table->dropColumn('partner_filter');
        });
    }
};
