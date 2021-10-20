<?php

use App\CasinoLog;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCasinoLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("casino_logs")) {
            Schema::create('casino_logs', function (Blueprint $table) {
                $table->id();
                $table->string('steam_identifier');
                $table->integer('character_id')->nullable(false);
                $table->enum('game', CasinoLog::ValidGames)->default(CasinoLog::GameBlackJack);
                $table->integer('money_spent')->nullable(false);
                $table->integer('money_earned')->nullable(false);
                $table->string('details');
                $table->timestamp('timestamp');
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
        Schema::drop('casino_logs');
    }
}
