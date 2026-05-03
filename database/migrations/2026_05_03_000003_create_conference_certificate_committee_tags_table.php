<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conference_certificate_committee_tags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conference_id');
            $table->unsignedBigInteger('committee_id');
            $table->unsignedBigInteger('designation_id')->nullable();
            $table->string('name_tag', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conference_certificate_committee_tags');
    }
};
