<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStocksCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks_companies', function (Blueprint $table) {
            $table->integer('company_id')->primary();
            $table->integer('owner_cid')->nullable();
            $table->longText('owner_name')->nullable();
            $table->longText('company_name')->nullable();
            $table->longText('company_description')->nullable();
            $table->longText('company_logo')->nullable();
            $table->double('company_balance')->nullable()->default(0);
            $table->timestamp('company_reg_timestamp')->useCurrent();
            $table->integer('total_shares')->nullable()->default(0);
            $table->integer('total_shares_purchased')->nullable()->default(0);
            $table->integer('max_shares')->nullable()->default(1000000);
            $table->double('share_price')->nullable()->default(0.01);
            $table->double('share_change')->nullable()->default(0);
            $table->integer('bankrupt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stocks_companies');
    }
}
