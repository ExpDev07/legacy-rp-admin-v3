<?php

use App\CasinoLog;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSystemScreenshotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("system_screenshots")) {
            Schema::create('system_screenshots', function (Blueprint $table) {
                $table->id();
                $table->integer('character_id')->nullable(false);
                $table->string('url');
                $table->string('details');
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
        Schema::drop('system_screenshots');
    }
}
