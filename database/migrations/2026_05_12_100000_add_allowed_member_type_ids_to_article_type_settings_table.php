<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('article_type_settings', function (Blueprint $table) {
            $table->json('allowed_member_type_ids')->nullable()->after('is_source_of_funding_required')
                ->comment('Null or empty means no restriction; otherwise only these member types can submit');
        });
    }

    public function down(): void
    {
        Schema::table('article_type_settings', function (Blueprint $table) {
            $table->dropColumn('allowed_member_type_ids');
        });
    }
};
