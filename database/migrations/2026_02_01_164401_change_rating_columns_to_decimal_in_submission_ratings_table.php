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
            // Change all rating columns from integer to decimal to support fractional scores
            $table->decimal('introduction', 5, 2)->nullable()->change();
            $table->decimal('method', 5, 2)->nullable()->change();
            $table->decimal('result', 5, 2)->nullable()->change();
            $table->decimal('conclusion', 5, 2)->nullable()->change();
            $table->decimal('grammar', 5, 2)->nullable()->change();
            $table->decimal('overall_rating', 5, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submission_ratings', function (Blueprint $table) {
            // Revert back to integer type
            $table->integer('introduction')->nullable()->change();
            $table->integer('method')->nullable()->change();
            $table->integer('result')->nullable()->change();
            $table->integer('conclusion')->nullable()->change();
            $table->integer('grammar')->nullable()->change();
            $table->integer('overall_rating')->nullable()->change();
        });
    }
};
