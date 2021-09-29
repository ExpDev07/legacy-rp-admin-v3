<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class updateWebpanelEconomyStatisticsTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('webpanel_economy_statistics', function (Blueprint $table) {
            $table->bigInteger('opening')->change();
            $table->bigInteger('closing')->change();
            $table->bigInteger('high')->change();
            $table->bigInteger('low')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('webpanel_economy_statistics', function (Blueprint $table) {
            $table->integer('opening')->change();
            $table->integer('closing')->change();
            $table->integer('high')->change();
            $table->integer('low')->change();
        });
    }
}
