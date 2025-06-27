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
        Schema::create('conference_organizers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('conference_id')->nullable();
            $table->string('organizer_name')->nullable();
            $table->string('organizer_logo')->nullable();
            $table->string('organizer_contact_person')->nullable();
            $table->string('organizer_email')->nullable();
            $table->string('organizer_phone_number')->nullable();
            $table->longText('organizer_description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conference_organizers');
    }
};
