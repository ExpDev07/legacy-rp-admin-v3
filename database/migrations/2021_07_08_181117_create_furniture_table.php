<?php

use Illuminate\Support\Facades\DB;
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
        if (!Schema::hasTable("furniture")) {
            Schema::create('furniture', function (Blueprint $table) {
                $table->integer('id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("furniture", "property_id")) {
            Schema::table("furniture", function (Blueprint $table) {
                $table->integer('property_id')->nullable()->default(null);
                $table->index('property_id');
            });
        }
        if (!Schema::hasColumn("furniture", "hash")) {
            Schema::table("furniture", function (Blueprint $table) {
                $table->bigInteger('hash')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("furniture", "coords")) {
            Schema::table("furniture", function (Blueprint $table) {
                $table->longText('coords')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("furniture", "storage_id")) {
            Schema::table("furniture", function (Blueprint $table) {
                $table->integer('storage_id')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("furniture", "storage_size")) {
            Schema::table("furniture", function (Blueprint $table) {
                $table->integer('storage_size')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("furniture", "weed_growth")) {
            Schema::table("furniture", function (Blueprint $table) {
                $table->integer('weed_growth')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("furniture", "weed_timestamp")) {
            Schema::table("furniture", function (Blueprint $table) {
                $table->integer('weed_timestamp')->nullable()->default(null);
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
        Schema::dropIfExists('furniture');
    }
}
