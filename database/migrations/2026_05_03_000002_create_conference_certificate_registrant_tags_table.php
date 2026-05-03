<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conference_certificate_registrant_tags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conference_id');
            $table->tinyInteger('registrant_type');
            $table->string('name_tag', 255);
            $table->timestamps();

            $table->unique(['conference_id', 'registrant_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conference_certificate_registrant_tags');
    }
};
