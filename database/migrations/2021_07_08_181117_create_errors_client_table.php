<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateErrorsClientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("errors_client")) {
            Schema::create('errors_client', function (Blueprint $table) {
                $table->integer('error_id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("errors_client", "steam_identifier")) {
            Schema::table("errors_client", function (Blueprint $table) {
                $table->string('steam_identifier', 50)->nullable()->default(null);
                $table->index('steam_identifier');
            });
        }
        if (!Schema::hasColumn("errors_client", "error_location")) {
            Schema::table("errors_client", function (Blueprint $table) {
                $table->longText('error_location')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("errors_client", "error_trace")) {
            Schema::table("errors_client", function (Blueprint $table) {
                $table->longText('error_trace')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("errors_client", "error_feedback")) {
            Schema::table("errors_client", function (Blueprint $table) {
                $table->longText('error_feedback')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("errors_client", "player_ping")) {
            Schema::table("errors_client", function (Blueprint $table) {
                $table->integer('player_ping')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("errors_client", "server_id")) {
            Schema::table("errors_client", function (Blueprint $table) {
                $table->integer('server_id')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("errors_client", "timestamp")) {
            Schema::table("errors_client", function (Blueprint $table) {
                $table->integer('timestamp')->nullable()->default(null);
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
        Schema::dropIfExists('errors_client');
    }
}
