<?php

use Illuminate\Support\Facades\DB;
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
        if (!Schema::hasTable("spy_devices")) {
            Schema::create('spy_devices', function (Blueprint $table) {
                $table->integer('device_id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("spy_devices", "device_type")) {
            Schema::table("spy_devices", function (Blueprint $table) {
                $table->integer('device_type')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("spy_devices", "device_activation_timestamp")) {
            Schema::table("spy_devices", function (Blueprint $table) {
                $table->integer('device_activation_timestamp')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("spy_devices", "entity_type")) {
            Schema::table("spy_devices", function (Blueprint $table) {
                $table->integer('entity_type')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("spy_devices", "entity_identifier")) {
            Schema::table("spy_devices", function (Blueprint $table) {
                $table->integer('entity_identifier')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("spy_devices", "entity_vector3")) {
            Schema::table("spy_devices", function (Blueprint $table) {
                $table->longText('entity_vector3')->nullable()->default(null);
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
        Schema::dropIfExists('spy_devices');
    }
}
