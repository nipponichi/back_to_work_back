<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProIdToAdOffers extends Migration
{
    public function up()
    {
        Schema::table('ad_offers', function (Blueprint $table) {
            $table->renameColumn('user_id', 'sender_id');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('ad_offers', function (Blueprint $table) {
            $table->dropForeign(['sender_id']);
            $table->dropForeign(['receiver_id']);
            $table->dropColumn('receiver_id');
        });
    }
}
