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
        Schema::table('workshops', function (Blueprint $table) {
            $table->string('schedule_plan_attachment')->nullable()->after('image');
            $table->foreignId('created_by')->nullable()->constrained('users')->after('slug');
            $table->enum('approval_status', ['pending', 'approved', 'rejected', 'correction_needed'])->default('approved')->after('status');
            $table->text('admin_remarks')->nullable()->after('approval_status');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->after('admin_remarks');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['schedule_plan_attachment', 'created_by', 'approval_status', 'admin_remarks', 'reviewed_by', 'reviewed_at']);
        });
    }
};
