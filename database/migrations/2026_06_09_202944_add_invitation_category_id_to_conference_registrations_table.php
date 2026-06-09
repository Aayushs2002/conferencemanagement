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
        Schema::table('conference_registrations', function (Blueprint $table) {
            $table->unsignedBigInteger('invitation_category_id')->nullable()->after('is_invited');
            $table->foreign('invitation_category_id')
                ->references('id')
                ->on('invitation_categories')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conference_registrations', function (Blueprint $table) {
            $table->dropForeign(['invitation_category_id']);
            $table->dropColumn('invitation_category_id');
        });
    }
};
