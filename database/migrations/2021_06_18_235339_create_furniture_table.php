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
            $table->integer('id')->nullable(false)->autoIncrement();
            $table->integer('property_id')->nullable()->default(null);
            $table->bigInteger('hash')->nullable()->default(null);
            $table->longText('coords')->nullable()->default(null);
            $table->integer('storage_id')->nullable()->default(null);
            $table->integer('storage_size')->nullable()->default(null);
            $table->integer('weed_growth')->nullable()->default(null);
            $table->integer('weed_timestamp')->nullable()->default(null);
            $table->unique(['property_id']);
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
