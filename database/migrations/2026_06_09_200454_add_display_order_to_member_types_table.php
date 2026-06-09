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
        Schema::table('member_types', function (Blueprint $table) {
            $table->integer('display_order')->nullable()->after('type');
        });

        $memberTypes = \Illuminate\Support\Facades\DB::table('member_types')
            ->orderBy('society_id')
            ->orderBy('delegate')
            ->orderBy('id')
            ->get();

        $sequence = [];
        foreach ($memberTypes as $memberType) {
            $sequence[$memberType->society_id] = ($sequence[$memberType->society_id] ?? 0) + 1;
            \Illuminate\Support\Facades\DB::table('member_types')
                ->where('id', $memberType->id)
                ->update(['display_order' => $sequence[$memberType->society_id]]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_types', function (Blueprint $table) {
            $table->dropColumn('display_order');
        });
    }
};
