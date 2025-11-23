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
            $table->json('sections')->nullable()->after('abstract_content');
            $table->string('image')->nullable()->after('sections');
            $table->text('conflict_of_interest')->nullable()->after('image');
            $table->text('source_of_funding')->nullable()->after('conflict_of_interest');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['sections', 'image', 'conflict_of_interest', 'source_of_funding']);
        });
    }
};
