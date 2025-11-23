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
        Schema::create('article_type_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_type_id')->constrained('article_types')->onDelete('cascade');
            
            // Section toggles
            $table->boolean('has_introduction')->default(false);
            $table->boolean('has_methods')->default(false);
            $table->boolean('has_results')->default(false);
            $table->boolean('has_conclusion')->default(false);
            
            // Word limits for each section
            $table->integer('introduction_word_limit')->nullable();
            $table->integer('methods_word_limit')->nullable();
            $table->integer('results_word_limit')->nullable();
            $table->integer('conclusion_word_limit')->nullable();
            
            // Instructions for each section
            $table->text('introduction_instruction')->nullable();
            $table->text('methods_instruction')->nullable();
            $table->text('results_instruction')->nullable();
            $table->text('conclusion_instruction')->nullable();
            
            // Attachment settings
            $table->string('attachment_name')->nullable();
            
            // Author limit
            $table->integer('author_limit')->nullable();
            
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_type_settings');
    }
};
