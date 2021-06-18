<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserBansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_bans', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('ban_hash', 50)->nullable();
            $table->string('identifier', 50)->nullable();
            $table->string('smurf_account', 50)->nullable();
            $table->longText('creator_name')->nullable();
            $table->longText('reason')->nullable();
            $table->integer('timestamp')->nullable();
            $table->integer('expire')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_bans');
    }
}
