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
            $table->boolean('title_scoring_enabled')->default(0)->after('scoring_allowed');
            $table->decimal('title_max_marks', 5, 2)->default(0)->after('title_scoring_enabled');
            $table->text('title_reviewer_instruction')->nullable()->after('title_max_marks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('article_type_settings', function (Blueprint $table) {
            $table->dropColumn(['title_scoring_enabled', 'title_max_marks', 'title_reviewer_instruction']);
        });
    }
};
