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
        Schema::create('adds', function (Blueprint $table) {
            $table->id();
            $table->string('name', 32);
            $table->string('phone', 16)->nullable();
            $table->integer('age')->nullable();
            $table->string('category', 64);
            $table->string('short_des', 64);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adds');
    }
};
