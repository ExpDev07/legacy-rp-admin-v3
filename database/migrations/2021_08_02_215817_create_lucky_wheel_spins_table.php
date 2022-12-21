<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLuckyWheelSpinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("lucky_wheel_spins")) {
            Schema::create('lucky_wheel_spins', function (Blueprint $table) {
                $table->bigInteger('id')->unsigned()->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("lucky_wheel_spins", "license_identifier")) {
            Schema::table("lucky_wheel_spins", function (Blueprint $table) {
                $table->string('license_identifier')->nullable(false);
            });
        }
        if (!Schema::hasColumn("lucky_wheel_spins", "paid_spin")) {
            Schema::table("lucky_wheel_spins", function (Blueprint $table) {
                $table->tinyInteger('paid_spin')->nullable(false)->default(0);
            });
        }
        if (!Schema::hasColumn("lucky_wheel_spins", "timestamp")) {
            Schema::table("lucky_wheel_spins", function (Blueprint $table) {
                $table->integer('timestamp')->nullable(false);
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
        Schema::dropIfExists('lucky_wheel_spins');
    }
}
