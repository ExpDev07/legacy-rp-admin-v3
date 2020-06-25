<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('steam_identifier');
            $table->string('player_name');
            $table->boolean('is_staff')->default(false);
            $table->boolean('is_super_admin')->default(false);
            $table->integer('playtime')->default(0);
            $table->timestamp('last_connection');

            // Json values.
            $table->json('identifiers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }

}
