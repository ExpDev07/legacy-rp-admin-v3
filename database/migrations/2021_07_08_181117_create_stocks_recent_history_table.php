<?php

use Illuminate\Support\Facades\DB;
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
        if (!Schema::hasTable("stocks_recent_history")) {
            Schema::create('stocks_recent_history', function (Blueprint $table) {
                $table->integer('id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("stocks_recent_history", "company_id")) {
            Schema::table("stocks_recent_history", function (Blueprint $table) {
                $table->integer('company_id')->nullable()->default(null);
                $table->index('company_id');
            });
        }
        if (!Schema::hasColumn("stocks_recent_history", "cid")) {
            Schema::table("stocks_recent_history", function (Blueprint $table) {
                $table->integer('cid')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_recent_history", "name")) {
            Schema::table("stocks_recent_history", function (Blueprint $table) {
                $table->longText('name')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_recent_history", "text")) {
            Schema::table("stocks_recent_history", function (Blueprint $table) {
                $table->longText('text')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_recent_history", "paid_amount")) {
            Schema::table("stocks_recent_history", function (Blueprint $table) {
                $table->integer('paid_amount')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_recent_history", "share_change")) {
            Schema::table("stocks_recent_history", function (Blueprint $table) {
                $table->double('share_change')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_recent_history", "timestamp")) {
            Schema::table("stocks_recent_history", function (Blueprint $table) {
                $table->timestamp('timestamp')->nullable(false)->useCurrent();
            });
        }
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
