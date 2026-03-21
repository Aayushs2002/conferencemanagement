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
        Schema::table('member_types', function (Blueprint $table) {
            $table->tinyInteger('requires_student_verification')->default(0)
                  ->after('is_society_member')
                  ->comment('1 if this member type requires student/resident verification documents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_types', function (Blueprint $table) {
            $table->dropColumn('requires_student_verification');
        });
    }
};
