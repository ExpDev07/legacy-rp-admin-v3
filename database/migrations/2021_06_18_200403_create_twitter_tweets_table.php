<?php

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
        Schema::create('twitter_tweets', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('authorId')->nullable();
            $table->string('realUser', 50)->nullable();
            $table->string('message', 256)->nullable();
            $table->timestamp('time')->useCurrent();
            $table->integer('likes')->nullable()->default(0);
        });
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
