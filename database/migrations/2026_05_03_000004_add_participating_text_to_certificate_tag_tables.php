<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conference_certificate_registrant_tags', function (Blueprint $table) {
            $table->string('participating_text', 255)->nullable()->after('name_tag');
        });

        Schema::table('conference_certificate_committee_tags', function (Blueprint $table) {
            $table->string('participating_text', 255)->nullable()->after('name_tag');
        });
    }

    public function down(): void
    {
        Schema::table('conference_certificate_registrant_tags', function (Blueprint $table) {
            $table->dropColumn('participating_text');
        });

        Schema::table('conference_certificate_committee_tags', function (Blueprint $table) {
            $table->dropColumn('participating_text');
        });
    }
};
