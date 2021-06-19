<?php

use Illuminate\Support\Facades\DB;
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
            $table->integer('error_id')->nullable(false)->autoIncrement();
            $table->longText('error_location')->nullable()->default(null);
            $table->longText('error_trace')->nullable()->default(null);
            $table->integer('server_id')->nullable()->default(null);
            $table->integer('timestamp')->nullable()->default(null);
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
