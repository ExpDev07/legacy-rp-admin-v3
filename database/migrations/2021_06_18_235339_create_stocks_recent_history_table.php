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
            $table->integer('id')->nullable(false)->autoIncrement();
            $table->integer('company_id')->nullable()->default(null);
            $table->integer('cid')->nullable()->default(null);
            $table->longText('name')->nullable()->default(null);
            $table->longText('text')->nullable()->default(null);
            $table->integer('paid_amount')->nullable()->default(null);
            $table->double('share_change')->nullable()->default(null);
            $table->timestamp('timestamp')->nullable(false)->useCurrent();
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
        Schema::dropIfExists('stocks_recent_history');
    }
}
