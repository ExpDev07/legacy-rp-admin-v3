<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserBansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("user_bans")) {
            Schema::create('user_bans', function (Blueprint $table) {
                $table->integer('id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("user_bans", "ban_hash")) {
            Schema::table("user_bans", function (Blueprint $table) {
                $table->string('ban_hash', 50)->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("user_bans", "identifier")) {
            Schema::table("user_bans", function (Blueprint $table) {
                $table->string('identifier', 50)->nullable()->default(null);
                $table->index('identifier');
            });
        }
        if (!Schema::hasColumn("user_bans", "smurf_account")) {
            Schema::table("user_bans", function (Blueprint $table) {
                $table->string('smurf_account', 50)->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("user_bans", "creator_name")) {
            Schema::table("user_bans", function (Blueprint $table) {
                $table->longText('creator_name')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("user_bans", "reason")) {
            Schema::table("user_bans", function (Blueprint $table) {
                $table->longText('reason')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("user_bans", "timestamp")) {
            Schema::table("user_bans", function (Blueprint $table) {
                $table->integer('timestamp')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("user_bans", "expire")) {
            Schema::table("user_bans", function (Blueprint $table) {
                $table->integer('expire')->nullable()->default(null);
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
        Schema::dropIfExists('user_bans');
    }
}
