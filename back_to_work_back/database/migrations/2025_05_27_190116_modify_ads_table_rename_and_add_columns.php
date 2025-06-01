<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyAdsTableRenameAndAddColumns extends Migration
{
    public function up()
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->renameColumn('is_done', 'pro_is_done');
            $table->boolean('customer_is_done')->default(false);
        });
    }

    public function down()
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->renameColumn('pro_is_done', 'is_done');
            $table->dropColumn('customer_is_done');
        });
    }
}