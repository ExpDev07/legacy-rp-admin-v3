<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStocksCompanyPropertiesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stocks_company_properties', function (Blueprint $table) {
            $table->integer('property_id');
            $table->integer('property_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stocks_company_properties', function (Blueprint $table) {
            $table->dropColumn('property_id');
        });
    }

}
