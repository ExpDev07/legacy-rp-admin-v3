<?php

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
        Schema::create('dealership_cars', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('slot')->nullable()->default(-1);
            $table->integer('commission')->nullable()->default(0);
            $table->integer('commission_cid')->nullable();
            $table->longText('category')->nullable();
            $table->integer('category_id')->nullable()->default(0);
        });
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
