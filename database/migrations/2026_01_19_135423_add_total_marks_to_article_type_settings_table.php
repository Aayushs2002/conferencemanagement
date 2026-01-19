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
            $table->decimal('total_marks', 5, 2)->default(10)->after('sections')->comment('Total marks for this article type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('article_type_settings', function (Blueprint $table) {
            $table->dropColumn('total_marks');
        });
    }
};
