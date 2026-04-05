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
        Schema::create('submission_category_major_track_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('submission_category_major_track_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->unique(
                ['submission_category_major_track_id', 'user_id'],
                'submission_track_user_unique'
            );

            $table->foreign('submission_category_major_track_id', 'submission_track_user_track_fk')
                ->references('id')
                ->on('submission_category_major_tracks')
                ->onDelete('cascade');

            $table->foreign('user_id', 'submission_track_user_user_fk')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_category_major_track_user');
    }
};
