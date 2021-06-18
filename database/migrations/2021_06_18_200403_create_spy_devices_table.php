<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpyDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spy_devices', function (Blueprint $table) {
            $table->integer('device_id')->primary();
            $table->integer('device_type')->nullable();
            $table->integer('device_activation_timestamp')->nullable();
            $table->integer('entity_type')->nullable();
            $table->integer('entity_identifier')->nullable();
            $table->longText('entity_vector3')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spy_devices');
    }
}
