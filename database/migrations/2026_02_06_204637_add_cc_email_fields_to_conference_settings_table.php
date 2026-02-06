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
        Schema::table('conference_settings', function (Blueprint $table) {
            $table->text('submission_cc_emails')->nullable()->after('addon_availability');
            $table->text('reviewer_assignment_cc_emails')->nullable()->after('submission_cc_emails');
            $table->text('conference_registration_cc_emails')->nullable()->after('reviewer_assignment_cc_emails');
            $table->text('workshop_registration_cc_emails')->nullable()->after('conference_registration_cc_emails');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conference_settings', function (Blueprint $table) {
            $table->dropColumn([
                'submission_cc_emails',
                'reviewer_assignment_cc_emails',
                'conference_registration_cc_emails',
                'workshop_registration_cc_emails'
            ]);
        });
    }
};
