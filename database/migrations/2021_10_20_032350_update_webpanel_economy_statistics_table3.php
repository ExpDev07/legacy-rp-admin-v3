<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class UpdateWebpanelEconomyStatisticsTable3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Delete everything before Wed Oct 20 2021 01:40:00 GMT+0000 because we added vehicles to it
        DB::table('webpanel_economy_statistics')->where('last_updated', '<', 1634694000)->delete();
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
