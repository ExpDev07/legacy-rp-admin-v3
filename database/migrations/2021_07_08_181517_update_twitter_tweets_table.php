<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTwitterTweetsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('twitter_tweets', 'is_deleted')) {
            Schema::table('twitter_tweets', function (Blueprint $table) {
                $table->tinyInteger('is_deleted')->default(0);
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
        Schema::table('twitter_tweets', function (Blueprint $table) {
            $table->dropColumn('is_deleted');
        });
    }

}
