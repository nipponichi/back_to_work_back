<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsProAndProvinceIdToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_pro')->default(false);
            $table->unsignedBigInteger('province_id')->nullable();
            
            $table->foreign('province_id')
                  ->references('id')
                  ->on('provinces')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['province_id']);
            $table->dropColumn(['is_pro', 'province_id']);
        });
    }
}