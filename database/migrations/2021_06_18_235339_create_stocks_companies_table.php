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
            $table->integer('company_id')->nullable(false)->autoIncrement();
            $table->integer('owner_cid')->nullable()->default(null);
            $table->longText('owner_name')->nullable()->default(null);
            $table->longText('company_name')->nullable()->default(null);
            $table->longText('company_description')->nullable()->default(null);
            $table->longText('company_logo')->nullable()->default(null);
            $table->double('company_balance')->default(0);
            $table->timestamp('company_reg_timestamp')->nullable(false)->useCurrent();
            $table->integer('total_shares')->default(0);
            $table->integer('total_shares_purchased')->default(0);
            $table->integer('max_shares')->default(1000000);
            $table->double('share_price')->default(0.01);
            $table->double('share_change')->default(0);
            $table->integer('bankrupt')->nullable()->default(null);
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
        Schema::dropIfExists('stocks_companies');
    }
}
