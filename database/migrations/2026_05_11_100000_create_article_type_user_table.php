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
        Schema::create('article_type_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_type_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->unique(
                ['article_type_id', 'user_id'],
                'article_type_user_unique'
            );

            $table->foreign('article_type_id', 'article_type_user_at_fk')
                ->references('id')
                ->on('article_types')
                ->onDelete('cascade');

            $table->foreign('user_id', 'article_type_user_user_fk')
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
        Schema::dropIfExists('article_type_user');
    }
};
