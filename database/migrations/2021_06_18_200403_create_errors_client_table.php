<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateErrorsClientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('errors_client', function (Blueprint $table) {
            $table->integer('error_id')->primary();
            $table->string('steam_identifier', 50)->nullable();
            $table->longText('error_location')->nullable();
            $table->longText('error_trace')->nullable();
            $table->longText('error_feedback')->nullable();
            $table->integer('player_ping')->nullable();
            $table->integer('server_id')->nullable();
            $table->integer('timestamp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('errors_client');
    }
}
