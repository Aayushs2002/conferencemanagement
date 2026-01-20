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
        Schema::table('article_type_settings', function (Blueprint $table) {
            $table->boolean('scoring_allowed')->default(true)->after('total_marks')->comment('Whether scoring is enabled for this article type');
            $table->text('overall_instruction')->nullable()->after('scoring_allowed')->comment('Instructions for reviewers when rating overall score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('article_type_settings', function (Blueprint $table) {
            $table->dropColumn(['scoring_allowed', 'overall_instruction']);
        });
    }
};
