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
        Schema::create('adds_pictures', function (Blueprint $table) {
            $table->id();
            $table->string('path'); // Almacena la ruta del archivo
            $table->string('type'); // "image" o "video"
            $table->foreignId('add_id')->constrained('adds')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adds_pictures');
    }
};
