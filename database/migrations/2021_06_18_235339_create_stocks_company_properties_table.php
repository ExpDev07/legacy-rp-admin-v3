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
            $table->integer('id')->nullable(false)->autoIncrement();
            $table->integer('company_id')->nullable()->default(null);
            $table->integer('block_id')->nullable()->default(null);
            $table->integer('property_id')->nullable()->default(null);
            $table->longText('property_name')->nullable()->default(null);
            $table->tinyInteger('property_type')->nullable()->default(null);
            $table->longText('property_address')->nullable()->default(null);
            $table->integer('property_cost')->nullable()->default(null);
            $table->longText('property_renter')->nullable()->default(null);
            $table->integer('property_renter_cid')->nullable()->default(null);
            $table->integer('property_income')->nullable()->default(null);
            $table->integer('property_last_pay')->nullable()->default(null);
            $table->longText('shared_keys')->nullable()->default(null);
            $table->tinyInteger('furniture_enabled')->default(1);
            $table->integer('furniture_last_pay')->nullable()->default(null);
            $table->integer('terminate')->nullable()->default(null);
            $table->unique(['company_id']);
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
