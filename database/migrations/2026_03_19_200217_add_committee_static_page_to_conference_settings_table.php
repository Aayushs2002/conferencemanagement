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
        Schema::table('conference_settings', function (Blueprint $table) {
            $table->boolean('committee_static_page_enabled')->default(false)->after('closing_message');
            $table->longText('committee_static_page_content')->nullable()->after('committee_static_page_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conference_settings', function (Blueprint $table) {
            $table->dropColumn(['committee_static_page_enabled', 'committee_static_page_content']);
        });
    }
};
