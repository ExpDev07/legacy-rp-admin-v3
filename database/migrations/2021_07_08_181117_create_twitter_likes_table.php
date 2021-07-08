<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwitterLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("twitter_likes")) {
            Schema::create('twitter_likes', function (Blueprint $table) {
                $table->integer('id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("twitter_likes", "authorId")) {
            Schema::table("twitter_likes", function (Blueprint $table) {
                $table->integer('authorId')->nullable()->default(null);
                $table->index('authorId');
            });
        }
        if (!Schema::hasColumn("twitter_likes", "tweetId")) {
            Schema::table("twitter_likes", function (Blueprint $table) {
                $table->integer('tweetId')->nullable()->default(null);
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
        Schema::dropIfExists('twitter_likes');
    }
}
