<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwitterAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("twitter_accounts")) {
            Schema::create('twitter_accounts', function (Blueprint $table) {
                $table->integer('id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("twitter_accounts", "username")) {
            Schema::table("twitter_accounts", function (Blueprint $table) {
                $table->string('username', 50)->default('0');
                $table->index('username');
            });
        }
        if (!Schema::hasColumn("twitter_accounts", "password")) {
            Schema::table("twitter_accounts", function (Blueprint $table) {
                $table->string('password', 50)->default('0');
            });
        }
        if (!Schema::hasColumn("twitter_accounts", "avatar_url")) {
            Schema::table("twitter_accounts", function (Blueprint $table) {
                $table->string('avatar_url', 255)->nullable()->default(null);
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
        Schema::dropIfExists('twitter_accounts');
    }
}
