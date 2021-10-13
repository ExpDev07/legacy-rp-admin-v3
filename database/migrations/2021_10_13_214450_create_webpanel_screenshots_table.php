<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebpanelScreenshotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("webpanel_screenshots")) {
            Schema::create('webpanel_screenshots', function (Blueprint $table) {
                $table->id();
                $table->string('steam_identifier')->nullable(false);
                $table->string('filename')->nullable(false);
                $table->string('note');
                $table->integer('created_at');
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
        Schema::drop('webpanel_screenshots');
    }
}
