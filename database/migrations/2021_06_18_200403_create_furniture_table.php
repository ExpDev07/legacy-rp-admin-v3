<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFurnitureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('furniture', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('property_id')->nullable();
            $table->bigInteger('hash')->nullable();
            $table->longText('coords')->nullable();
            $table->integer('storage_id')->nullable();
            $table->integer('storage_size')->nullable();
            $table->integer('weed_growth')->nullable();
            $table->integer('weed_timestamp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('furniture');
    }
}
