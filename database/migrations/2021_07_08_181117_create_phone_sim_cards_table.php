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
        if (!Schema::hasTable("phone_sim_cards")) {
            Schema::create('phone_sim_cards', function (Blueprint $table) {
                $table->integer('sim_card_id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("phone_sim_cards", "phone_id")) {
            Schema::table("phone_sim_cards", function (Blueprint $table) {
                $table->integer('phone_id')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("phone_sim_cards", "character_id")) {
            Schema::table("phone_sim_cards", function (Blueprint $table) {
                $table->integer('character_id')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("phone_sim_cards", "phone_settings")) {
            Schema::table("phone_sim_cards", function (Blueprint $table) {
                $table->longText('phone_settings')->nullable()->default(null);
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
        Schema::dropIfExists('phone_sim_cards');
    }
}
