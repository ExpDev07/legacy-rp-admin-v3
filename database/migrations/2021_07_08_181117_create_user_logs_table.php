<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("user_logs")) {
            Schema::create('user_logs', function (Blueprint $table) {
                $table->integer('id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("user_logs", "identifier")) {
            Schema::table("user_logs", function (Blueprint $table) {
                $table->string('identifier', 50)->nullable()->default(null);
                $table->index('identifier');
            });
        }
        if (!Schema::hasColumn("user_logs", "action")) {
            Schema::table("user_logs", function (Blueprint $table) {
                $table->string('action', 50)->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("user_logs", "details")) {
            Schema::table("user_logs", function (Blueprint $table) {
                $table->longText('details')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("user_logs", "metadata")) {
            Schema::table("user_logs", function (Blueprint $table) {
                $table->longText('metadata')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("user_logs", "timestamp")) {
            Schema::table("user_logs", function (Blueprint $table) {
                $table->timestamp('timestamp')->nullable(false)->useCurrent();
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
        Schema::dropIfExists('user_logs');
    }
}
