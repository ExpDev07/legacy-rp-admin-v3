<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStocksCompanyPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks_company_properties', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('company_id')->nullable();
            $table->integer('block_id')->nullable();
            $table->integer('property_id')->nullable();
            $table->longText('property_name')->nullable();
            $table->tinyInteger('property_type')->nullable();
            $table->longText('property_address')->nullable();
            $table->integer('property_cost')->nullable();
            $table->longText('property_renter')->nullable();
            $table->integer('property_renter_cid')->nullable();
            $table->integer('property_income')->nullable();
            $table->integer('property_last_pay')->nullable();
            $table->longText('shared_keys')->nullable();
            $table->boolean('furniture_enabled')->nullable()->default(1);
            $table->integer('furniture_last_pay')->nullable();
            $table->integer('terminate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stocks_company_properties');
    }
}
