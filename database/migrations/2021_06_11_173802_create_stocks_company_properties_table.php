<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id('id');
            $table->text('property_name');
            $table->text('property_address');
            $table->integer('property_cost');
            $table->text('property_renter');
            $table->integer('property_renter_cid');
            $table->integer('property_income');
            $table->text('shared_keys');
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
