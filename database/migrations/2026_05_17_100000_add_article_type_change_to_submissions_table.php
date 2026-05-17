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
            // article_type_change: null = no request, 0 = request sent, 1 = accepted, 2 = rejected
            $table->tinyInteger('article_type_change')->nullable()->after('article_type_id');
            // The article type requested by admin
            $table->bigInteger('requested_article_type_id')->nullable()->after('article_type_change');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['article_type_change', 'requested_article_type_id']);
        });
    }
};
