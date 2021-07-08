<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwitterTweetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("twitter_tweets")) {
            Schema::create('twitter_tweets', function (Blueprint $table) {
                $table->integer('id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("twitter_tweets", "authorId")) {
            Schema::table("twitter_tweets", function (Blueprint $table) {
                $table->integer('authorId')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("twitter_tweets", "realUser")) {
            Schema::table("twitter_tweets", function (Blueprint $table) {
                $table->string('realUser', 50)->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("twitter_tweets", "message")) {
            Schema::table("twitter_tweets", function (Blueprint $table) {
                $table->string('message', 256)->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("twitter_tweets", "time")) {
            Schema::table("twitter_tweets", function (Blueprint $table) {
                $table->timestamp('time')->nullable(false)->useCurrent();
                $table->index('time');
            });
        }
        if (!Schema::hasColumn("twitter_tweets", "likes")) {
            Schema::table("twitter_tweets", function (Blueprint $table) {
                $table->integer('likes')->default(0);
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
        Schema::dropIfExists('twitter_tweets');
    }
}
