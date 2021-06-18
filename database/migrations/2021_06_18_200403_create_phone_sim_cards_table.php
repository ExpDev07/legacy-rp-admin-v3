<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhoneSimCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phone_sim_cards', function (Blueprint $table) {
            $table->integer('sim_card_id')->primary();
            $table->integer('phone_id')->nullable();
            $table->integer('character_id')->nullable();
            $table->longText('phone_settings')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('phone_sim_cards');
    }
}
