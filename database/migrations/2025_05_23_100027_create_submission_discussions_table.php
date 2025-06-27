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
        Schema::create('submission_discussions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('submission_id')->nullable();
            $table->bigInteger('sender_id')->nullable();
            $table->longText('remarks')->nullable();
            $table->string('attachment')->nullable();
            $table->bigInteger('committee_member_id')->nullable();
            $table->longText('committee_remarks')->nullable();
            $table->boolean('expert_visible')->default(0);
            $table->boolean('presenter_visible')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_discussions');
    }
};
