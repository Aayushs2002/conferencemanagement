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
        Schema::table('user_societies', function (Blueprint $table) {
            $table->string('id_card_document')->nullable()->after('member_type_id');
            $table->string('official_letter_document')->nullable()->after('id_card_document');
            $table->timestamp('documents_uploaded_at')->nullable()->after('official_letter_document');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_societies', function (Blueprint $table) {
            $table->dropColumn(['id_card_document', 'official_letter_document', 'documents_uploaded_at']);
        });
    }
};
