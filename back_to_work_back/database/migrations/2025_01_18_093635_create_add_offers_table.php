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
        Schema::create('adds_offers', function (Blueprint $table) {
            $table->id();
            $table->float('bid');
            $table->string('description')->nullable();
            $table->boolean('is_valid')->default(true);
            $table->foreignId('add_id')->constrained('adds')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adds_offers');
    }
};
