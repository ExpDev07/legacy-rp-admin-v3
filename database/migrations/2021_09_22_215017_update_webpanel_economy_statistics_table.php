<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class updateWebpanelEconomyStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Delete everything before Wed Sep 22 2021 18:10:00 GMT+0000 cause scuff
        DB::table('webpanel_economy_statistics')->where('last_updated', '<', 1632334200)->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
