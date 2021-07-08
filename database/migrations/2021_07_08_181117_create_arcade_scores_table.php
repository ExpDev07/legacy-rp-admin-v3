<?php

use Illuminate\Support\Facades\DB;
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
        if (!Schema::hasTable("arcade_scores")) {
            Schema::create('arcade_scores', function (Blueprint $table) {
                $table->integer('score_id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("arcade_scores", "character_id")) {
            Schema::table("arcade_scores", function (Blueprint $table) {
                $table->integer('character_id')->nullable()->default(null);
                $table->index('character_id');
            });
        }
        if (!Schema::hasColumn("arcade_scores", "game_name")) {
            Schema::table("arcade_scores", function (Blueprint $table) {
                $table->longText('game_name')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("arcade_scores", "score")) {
            Schema::table("arcade_scores", function (Blueprint $table) {
                $table->integer('score')->nullable()->default(null);
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
        Schema::dropIfExists('arcade_scores');
    }
}
