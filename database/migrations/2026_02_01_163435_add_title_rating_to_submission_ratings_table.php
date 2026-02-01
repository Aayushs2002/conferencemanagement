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
        Schema::table('submission_ratings', function (Blueprint $table) {
            $table->decimal('title_rating', 5, 2)->nullable()->after('submission_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submission_ratings', function (Blueprint $table) {
            $table->dropColumn('title_rating');
        });
    }
};
