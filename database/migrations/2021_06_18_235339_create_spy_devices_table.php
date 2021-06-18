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
            $table->integer('device_id')->nullable(false)->autoIncrement();
            $table->integer('device_type')->nullable()->default(null);
            $table->integer('device_activation_timestamp')->nullable()->default(null);
            $table->integer('entity_type')->nullable()->default(null);
            $table->integer('entity_identifier')->nullable()->default(null);
            $table->longText('entity_vector3')->nullable()->default(null);
            $table->unique(['device_id']);
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
