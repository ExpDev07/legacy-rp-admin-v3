<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_vehicles', function (Blueprint $table) {
            $table->id('vehicle_id');
            $table->foreignId('owner_cid');
            $table->text('garage_name');
            $table->text('model_name');
            $table->char('plate', 8)->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('character_vehicles');
    }

}
