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
        if (!Schema::hasTable("character_vehicles")) {
            Schema::create('character_vehicles', function (Blueprint $table) {
                $table->integer('vehicle_id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("character_vehicles", "owner_cid")) {
            Schema::table("character_vehicles", function (Blueprint $table) {
                $table->integer('owner_cid')->nullable()->default(null);
                $table->index('owner_cid');
            });
        }
        if (!Schema::hasColumn("character_vehicles", "model_name")) {
            Schema::table("character_vehicles", function (Blueprint $table) {
                $table->longText('model_name')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("character_vehicles", "plate")) {
            Schema::table("character_vehicles", function (Blueprint $table) {
                $table->char('plate', 8)->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("character_vehicles", "mileage")) {
            Schema::table("character_vehicles", function (Blueprint $table) {
                $table->double('mileage')->default(0);
            });
        }
        if (!Schema::hasColumn("character_vehicles", "modifications")) {
            Schema::table("character_vehicles", function (Blueprint $table) {
                $table->longText('modifications')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("character_vehicles", "data")) {
            Schema::table("character_vehicles", function (Blueprint $table) {
                $table->longText('data')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("character_vehicles", "garage_identifier")) {
            Schema::table("character_vehicles", function (Blueprint $table) {
                $table->longText('garage_identifier')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("character_vehicles", "garage_state")) {
            Schema::table("character_vehicles", function (Blueprint $table) {
                $table->integer('garage_state')->default(0);
            });
        }
        if (!Schema::hasColumn("character_vehicles", "garage_impound")) {
            Schema::table("character_vehicles", function (Blueprint $table) {
                $table->integer('garage_impound')->default(0);
            });
        }
        if (!Schema::hasColumn("character_vehicles", "deprecated_damage")) {
            Schema::table("character_vehicles", function (Blueprint $table) {
                $table->longText('deprecated_damage')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("character_vehicles", "deprecated_modifications")) {
            Schema::table("character_vehicles", function (Blueprint $table) {
                $table->longText('deprecated_modifications')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("character_vehicles", "deprecated_fuel")) {
            Schema::table("character_vehicles", function (Blueprint $table) {
                $table->double('deprecated_fuel')->default(100);
            });
        }
        if (!Schema::hasColumn("character_vehicles", "deprecated_supporter")) {
            Schema::table("character_vehicles", function (Blueprint $table) {
                $table->tinyInteger('deprecated_supporter')->default(0);
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
        Schema::dropIfExists('character_vehicles');
    }
}
