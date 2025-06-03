<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProIdToAdOffers extends Migration
{
    public function up()
    {
        Schema::table('ad_offers', function (Blueprint $table) {
            $table->foreignId('pro_id')->constrained('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('ad_offers', function (Blueprint $table) {
            $table->dropForeign(['pro_id']);
            $table->dropColumn('pro_id');
        });
    }
}
