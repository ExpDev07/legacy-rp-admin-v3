<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePanelIpInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("panel_ip_infos")) {
            Schema::create('panel_ip_infos', function (Blueprint $table) {
                $table->id();
                $table->string('ip');
                $table->string('country');
                $table->string('isp');
                $table->tinyInteger('proxy');
                $table->tinyInteger('hosting');
                $table->integer('last_crawled');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('panel_ip_infos');
    }
}
