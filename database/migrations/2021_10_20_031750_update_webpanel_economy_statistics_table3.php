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
        // Delete everything before Tue Oct 19 2021 23:16:00 GMT+0000 because we added vehicles to it
        DB::table('webpanel_economy_statistics')->where('last_updated', '<', 1634685360)->delete();
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
