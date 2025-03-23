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
            $table->text('path'); // Permite rutas más largas
            $table->enum('type', ['image', 'video']); // Restringe valores
            $table->foreignId('add_id')
                  ->constrained('adds')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
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
