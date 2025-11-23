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
        Schema::table('article_type_settings', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn([
                'has_introduction',
                'has_methods',
                'has_results',
                'has_conclusion',
                'introduction_word_limit',
                'methods_word_limit',
                'results_word_limit',
                'conclusion_word_limit',
                'introduction_instruction',
                'methods_instruction',
                'results_instruction',
                'conclusion_instruction'
            ]);

            // Add new columns
            $table->integer('number_of_sections')->default(0)->after('article_type_id');
            $table->json('sections')->nullable()->after('number_of_sections');
            $table->boolean('is_attachment_required')->default(false)->after('attachment_name');
            $table->boolean('is_conflict_of_interest_required')->default(false)->after('author_limit');
            $table->boolean('is_source_of_funding_required')->default(false)->after('is_conflict_of_interest_required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('article_type_settings', function (Blueprint $table) {
            // Add back old columns
            $table->boolean('has_introduction')->default(false);
            $table->boolean('has_methods')->default(false);
            $table->boolean('has_results')->default(false);
            $table->boolean('has_conclusion')->default(false);
            $table->integer('introduction_word_limit')->nullable();
            $table->integer('methods_word_limit')->nullable();
            $table->integer('results_word_limit')->nullable();
            $table->integer('conclusion_word_limit')->nullable();
            $table->text('introduction_instruction')->nullable();
            $table->text('methods_instruction')->nullable();
            $table->text('results_instruction')->nullable();
            $table->text('conclusion_instruction')->nullable();

            // Drop new columns
            $table->dropColumn([
                'number_of_sections',
                'sections',
                'is_attachment_required',
                'is_conflict_of_interest_required',
                'is_source_of_funding_required'
            ]);
        });
    }
};
