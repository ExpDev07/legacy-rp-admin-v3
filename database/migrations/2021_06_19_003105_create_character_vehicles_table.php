<?php

use Illuminate\Support\Facades\DB;
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
            $table->integer('vehicle_id')->nullable(false)->autoIncrement();
            $table->integer('owner_cid')->nullable()->default(null);
            $table->longText('model_name')->nullable()->default(null);
            $table->char('plate', 8)->nullable()->default(null);
            $table->double('mileage')->default(0);
            $table->longText('modifications')->nullable()->default(null);
            $table->longText('data')->nullable()->default(null);
            $table->longText('garage_identifier')->nullable()->default(null);
            $table->integer('garage_state')->default(0);
            $table->integer('garage_impound')->default(0);
            $table->longText('deprecated_damage')->nullable()->default(null);
            $table->longText('deprecated_modifications')->nullable()->default(null);
            $table->double('deprecated_fuel')->default(100);
            $table->tinyInteger('deprecated_supporter')->default(0);
            $table->index('owner_cid');
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
