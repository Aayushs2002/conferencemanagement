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
        Schema::create('conference_custom_css', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('conference_id');
            $table->string('section_name'); // navbar_logo, banner, footer, etc.
            $table->text('custom_css')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
            $table->unique(['conference_id', 'section_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conference_custom_css');
    }
};
