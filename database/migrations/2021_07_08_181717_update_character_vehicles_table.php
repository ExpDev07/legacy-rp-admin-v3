<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCharacterVehiclesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('character_vehicles', 'vehicle_deleted')) {
            Schema::table('character_vehicles', function (Blueprint $table) {
                $table->tinyInteger('vehicle_deleted')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('character_vehicles', function (Blueprint $table) {
            $table->dropColumn('vehicle_deleted');
        });
    }

}
