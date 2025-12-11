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
        Schema::table('conference_addons', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['addon_national_amount', 'addon_international_amount']);
            
            // Add new member_type_id column
            $table->bigInteger('member_type_id')->nullable()->after('addon_name');
            
            // Add amount columns similar to conference pricing
            $table->decimal('early_bird_amount', 10, 2)->nullable()->after('member_type_id');
            $table->decimal('regular_amount', 10, 2)->nullable()->after('early_bird_amount');
            $table->decimal('on_site_amount', 10, 2)->nullable()->after('regular_amount');
            $table->decimal('guest_amount', 10, 2)->nullable()->after('on_site_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conference_addons', function (Blueprint $table) {
            // Restore old columns
            $table->string('addon_national_amount')->nullable();
            $table->string('addon_international_amount')->nullable();
            
            // Drop new columns
            $table->dropColumn(['member_type_id', 'early_bird_amount', 'regular_amount', 'on_site_amount', 'guest_amount']);
        });
    }
};
