<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBansTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_bans', function (Blueprint $table) {
            $table->id('id');
            $table->string('ban_hash');
            $table->string('identifier');
            $table->string('smurf_account')->nullable();
            $table->string('creator_name')->nullable();
            $table->string('reason');
            $table->integer('expire')->nullable();
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
