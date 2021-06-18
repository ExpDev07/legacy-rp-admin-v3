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
            $table->integer('error_id')->nullable(false)->autoIncrement();
            $table->string('steam_identifier', 50)->nullable()->default(null);
            $table->longText('error_location')->nullable()->default(null);
            $table->longText('error_trace')->nullable()->default(null);
            $table->longText('error_feedback')->nullable()->default(null);
            $table->integer('player_ping')->nullable()->default(null);
            $table->integer('server_id')->nullable()->default(null);
            $table->integer('timestamp')->nullable()->default(null);
            $table->unique(['steam_identifier']);
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
