<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharacterVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_vehicles', function (Blueprint $table) {
            $table->integer('vehicle_id')->primary();
            $table->integer('owner_cid')->nullable();
            $table->longText('model_name')->nullable();
            $table->char('plate', 8)->nullable();
            $table->double('mileage')->nullable()->default(0);
            $table->longText('modifications')->nullable();
            $table->longText('data')->nullable();
            $table->longText('garage_identifier')->nullable();
            $table->integer('garage_state')->nullable()->default(0);
            $table->integer('garage_impound')->nullable()->default(0);
            $table->longText('deprecated_damage')->nullable();
            $table->longText('deprecated_modifications')->nullable();
            $table->double('deprecated_fuel')->nullable()->default(100);
            $table->boolean('deprecated_supporter')->nullable()->default(0);
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
