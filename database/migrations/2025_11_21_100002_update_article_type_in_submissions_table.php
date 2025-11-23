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
        Schema::table('submissions', function (Blueprint $table) {
            // Drop the old tinyInteger article_type column
            $table->dropColumn('article_type');
        });

        Schema::table('submissions', function (Blueprint $table) {
            // Add new foreignId article_type_id column
            $table->foreignId('article_type_id')->nullable()->after('submission_category_major_track_id')->constrained('article_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropForeign(['article_type_id']);
            $table->dropColumn('article_type_id');
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->tinyInteger('article_type')->nullable()->after('submission_category_major_track_id');
        });
    }
};
