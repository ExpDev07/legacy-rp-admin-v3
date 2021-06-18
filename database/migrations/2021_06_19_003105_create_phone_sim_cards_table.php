<?php

use Illuminate\Support\Facades\DB;
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
            $table->integer('sim_card_id')->nullable(false)->autoIncrement();
            $table->integer('phone_id')->nullable()->default(null);
            $table->integer('character_id')->nullable()->default(null);
            $table->longText('phone_settings')->nullable()->default(null);
            $table->index('sim_card_id');
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
