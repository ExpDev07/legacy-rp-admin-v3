<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebpanelBanStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("webpanel_ban_statistics")) {
            Schema::create('webpanel_ban_statistics', function (Blueprint $table) {
                $table->bigInteger('id')->unsigned()->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("webpanel_ban_statistics", "last_updated")) {
            Schema::table("webpanel_ban_statistics", function (Blueprint $table) {
                $table->integer('last_updated')->nullable(false);
            });
        }
        if (!Schema::hasColumn("webpanel_ban_statistics", "day")) {
            Schema::table("webpanel_ban_statistics", function (Blueprint $table) {
                $table->string('day', 50)->collation('utf8mb4_unicode_ci')->nullable(false);
            });
        }
        if (!Schema::hasColumn("webpanel_ban_statistics", "opening")) {
            Schema::table("webpanel_ban_statistics", function (Blueprint $table) {
                $table->integer('opening')->nullable(false);
            });
        }
        if (!Schema::hasColumn("webpanel_ban_statistics", "closing")) {
            Schema::table("webpanel_ban_statistics", function (Blueprint $table) {
                $table->integer('closing')->nullable(false);
            });
        }
        if (!Schema::hasColumn("webpanel_ban_statistics", "high")) {
            Schema::table("webpanel_ban_statistics", function (Blueprint $table) {
                $table->integer('high')->nullable(false);
            });
        }
        if (!Schema::hasColumn("webpanel_ban_statistics", "low")) {
            Schema::table("webpanel_ban_statistics", function (Blueprint $table) {
                $table->integer('low')->nullable(false);
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
        Schema::dropIfExists('webpanel_ban_statistics');
    }
}
