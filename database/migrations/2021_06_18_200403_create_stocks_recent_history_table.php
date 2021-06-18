<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStocksRecentHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks_recent_history', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('company_id')->nullable();
            $table->integer('cid')->nullable();
            $table->longText('name')->nullable();
            $table->longText('text')->nullable();
            $table->integer('paid_amount')->nullable();
            $table->double('share_change')->nullable();
            $table->timestamp('timestamp')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stocks_recent_history');
    }
}
