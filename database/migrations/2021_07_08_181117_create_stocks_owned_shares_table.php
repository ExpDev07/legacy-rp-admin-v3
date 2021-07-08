<?php

use Illuminate\Support\Facades\DB;
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
        if (!Schema::hasTable("stocks_owned_shares")) {
            Schema::create('stocks_owned_shares', function (Blueprint $table) {
                $table->integer('id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("stocks_owned_shares", "cid")) {
            Schema::table("stocks_owned_shares", function (Blueprint $table) {
                $table->integer('cid')->nullable()->default(null);
                $table->index('cid');
            });
        }
        if (!Schema::hasColumn("stocks_owned_shares", "company_id")) {
            Schema::table("stocks_owned_shares", function (Blueprint $table) {
                $table->integer('company_id')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_owned_shares", "share_amount")) {
            Schema::table("stocks_owned_shares", function (Blueprint $table) {
                $table->integer('share_amount')->nullable()->default(null);
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
        Schema::dropIfExists('stocks_owned_shares');
    }
}
