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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('country_id')->nullable();
            $table->bigInteger('name_prefix_id')->nullable();
            $table->bigInteger('institution_id')->nullable();
            $table->bigInteger('designation_id')->nullable();
            $table->bigInteger('department_id')->nullable();
            $table->string('institute_address')->nullable();
            $table->integer('gender')->nullable();
            $table->string('phone')->nullable();
            $table->string('image')->nullable();
            $table->string('council_number')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
