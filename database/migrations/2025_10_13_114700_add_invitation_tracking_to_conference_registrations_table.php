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
            $table->timestamp('invitation_accepted_at')->nullable()->after('is_invited');
            $table->string('invitation_response_token', 64)->nullable()->after('invitation_accepted_at');
            $table->index(['is_invited', 'invitation_accepted_at']);
            $table->index('invitation_response_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conference_registrations', function (Blueprint $table) {
            $table->dropIndex(['is_invited', 'invitation_accepted_at']);
            $table->dropIndex(['invitation_response_token']);
            $table->dropColumn(['invitation_accepted_at', 'invitation_response_token']);
        });
    }
};
