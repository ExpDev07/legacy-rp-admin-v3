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
            $table->boolean('is_soft_banned')->default(false);
            $table->integer('playtime')->default(0);
            $table->integer('total_joins')->default(0);
            $table->integer('priority_level')->default(0);

            // Timestamps.
            $table->timestamp('last_connection');

            // Json values.
            $table->json('identifiers')->nullable();
            $table->json('player_aliases')->nullable();
            $table->json('user_settings')->nullable();
            $table->json('user_data')->nullable();
            $table->json('staff_points')->nullable();
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
