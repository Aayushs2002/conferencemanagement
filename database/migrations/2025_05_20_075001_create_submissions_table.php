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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('conference_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('expert_id')->nullable();
            $table->bigInteger('submission_category_major_track_id')->nullable();
            $table->string('title')->nullable();
            $table->integer('article_type')->nullable();
            $table->integer('presentation_type')->nullable();
            $table->longText('keywords')->nullable();
            $table->longText('abstract_content')->nullable();
            $table->date('submitted_date')->nullable();
            $table->tinyInteger('request_status')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations. 
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
