<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDealershipCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("dealership_cars")) {
            Schema::create('dealership_cars', function (Blueprint $table) {
                $table->integer('id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("dealership_cars", "slot")) {
            Schema::table("dealership_cars", function (Blueprint $table) {
                $table->integer('slot')->default(-1);
                $table->index('slot');
            });
        }
        if (!Schema::hasColumn("dealership_cars", "commission")) {
            Schema::table("dealership_cars", function (Blueprint $table) {
                $table->integer('commission')->default(0);
            });
        }
        if (!Schema::hasColumn("dealership_cars", "commission_cid")) {
            Schema::table("dealership_cars", function (Blueprint $table) {
                $table->integer('commission_cid')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("dealership_cars", "category")) {
            Schema::table("dealership_cars", function (Blueprint $table) {
                $table->longText('category')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("dealership_cars", "category_id")) {
            Schema::table("dealership_cars", function (Blueprint $table) {
                $table->integer('category_id')->default(0);
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
        Schema::dropIfExists('dealership_cars');
    }
}
