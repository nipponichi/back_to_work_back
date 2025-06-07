<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClaimsTable extends Migration
{
    public function up()
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sender_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('receiver_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('ad_id')
                ->nullable()
                ->constrained('ads')
                ->onDelete('set null');

            $table->foreignId('bid_id')
                ->nullable()
                ->constrained('ads_offers')
                ->onDelete('set null');

            $table->foreignId('user_stats_id')
                ->nullable()
                ->constrained('user_stats')
                ->onDelete('set null');

            $table->string('images')->nullable();

            $table->text('reason');

            $table->string('status')->default('pending');

            $table->text('admin_notes')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('claims');
    }
}

