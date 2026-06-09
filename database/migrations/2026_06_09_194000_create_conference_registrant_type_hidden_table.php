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
        Schema::create('conference_registrant_type_hidden', function (Blueprint $table) {
            $table->unsignedBigInteger('conference_id');
            $table->unsignedBigInteger('registrant_type_id');
            $table->primary(['conference_id', 'registrant_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conference_registrant_type_hidden');
    }
};
