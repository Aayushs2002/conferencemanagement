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
        Schema::table('workshop_certificates', function (Blueprint $table) {
            // Check if columns don't exist before adding (safety check)
            if (!Schema::hasColumn('workshop_certificates', 'signature_image')) {
                $table->string('signature_image')->nullable();
            }
            if (!Schema::hasColumn('workshop_certificates', 'signature_name')) {
                $table->string('signature_name')->nullable();
            }
            if (!Schema::hasColumn('workshop_certificates', 'signature_designation')) {
                $table->string('signature_designation')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshop_certificates', function (Blueprint $table) {
            if (Schema::hasColumn('workshop_certificates', 'signature_image')) {
                $table->dropColumn('signature_image');
            }
            if (Schema::hasColumn('workshop_certificates', 'signature_name')) {
                $table->dropColumn('signature_name');
            }
            if (Schema::hasColumn('workshop_certificates', 'signature_designation')) {
                $table->dropColumn('signature_designation');
            }
        });
    }
};
