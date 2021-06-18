<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStocksOwnedSharesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks_owned_shares', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('cid')->nullable();
            $table->integer('company_id')->nullable();
            $table->integer('share_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stocks_owned_shares');
    }
}
