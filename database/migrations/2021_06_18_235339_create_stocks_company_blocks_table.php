<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStocksCompanyBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks_company_blocks', function (Blueprint $table) {
            $table->integer('id')->nullable(false)->autoIncrement();
            $table->integer('block_id')->nullable()->default(null);
            $table->integer('company_id')->nullable()->default(null);
            $table->integer('last_payment')->nullable()->default(null);
            $table->integer('next_payment')->nullable()->default(null);
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
        Schema::dropIfExists('stocks_company_blocks');
    }
}
