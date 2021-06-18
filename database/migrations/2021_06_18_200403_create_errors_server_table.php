<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateErrorsServerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('errors_server', function (Blueprint $table) {
            $table->integer('error_id')->primary();
            $table->longText('error_location')->nullable();
            $table->longText('error_trace')->nullable();
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
        Schema::dropIfExists('errors_server');
    }
}
