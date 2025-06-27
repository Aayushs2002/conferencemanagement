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
        Schema::create('submission_settings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('conference_id')->nullable();
            $table->date('deadline')->nullable();
            $table->smallInteger('abstract_word_limit')->nullable();
            $table->smallInteger('key_word_limit')->nullable();
            $table->smallInteger('authors_limit')->nullable();
            $table->longText('abstract_guidelines')->nullable();
            $table->longText('oral_guidelines')->nullable();
            $table->longText('poster_guidelines')->nullable();
            $table->longText('oral_reviewer_guide')->nullable();
            $table->longText('poster_reviewer_guide')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_settings');
    }
};
