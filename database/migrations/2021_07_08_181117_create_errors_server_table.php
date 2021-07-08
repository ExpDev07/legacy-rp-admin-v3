<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateErrorsServerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("errors_server")) {
            Schema::create('errors_server', function (Blueprint $table) {
                $table->integer('error_id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("errors_server", "error_location")) {
            Schema::table("errors_server", function (Blueprint $table) {
                $table->longText('error_location')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("errors_server", "error_trace")) {
            Schema::table("errors_server", function (Blueprint $table) {
                $table->longText('error_trace')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("errors_server", "server_id")) {
            Schema::table("errors_server", function (Blueprint $table) {
                $table->integer('server_id')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("errors_server", "timestamp")) {
            Schema::table("errors_server", function (Blueprint $table) {
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
        Schema::dropIfExists('errors_server');
    }
}
