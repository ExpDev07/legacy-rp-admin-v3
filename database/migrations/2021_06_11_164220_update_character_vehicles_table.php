<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::table('character_vehicles', function (Blueprint $table) {
            $table->renameColumn('garage_name', 'garage_identifier');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('character_vehicles', function (Blueprint $table) {
            $table->renameColumn('garage_identifier', 'garage_name');
        });
    }

}
