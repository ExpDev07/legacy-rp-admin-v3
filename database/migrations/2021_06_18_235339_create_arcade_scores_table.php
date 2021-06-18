<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArcadeScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('arcade_scores', function (Blueprint $table) {
            $table->integer('score_id')->nullable(false)->autoIncrement();
            $table->integer('character_id')->nullable()->default(null);
            $table->longText('game_name')->nullable()->default(null);
            $table->integer('score')->nullable()->default(null);
            $table->unique(['character_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('arcade_scores');
    }
}
