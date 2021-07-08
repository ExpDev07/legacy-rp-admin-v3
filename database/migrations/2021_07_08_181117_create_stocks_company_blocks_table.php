<?php

use Illuminate\Support\Facades\DB;
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
        if (!Schema::hasTable("stocks_company_blocks")) {
            Schema::create('stocks_company_blocks', function (Blueprint $table) {
                $table->integer('id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("stocks_company_blocks", "block_id")) {
            Schema::table("stocks_company_blocks", function (Blueprint $table) {
                $table->integer('block_id')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_company_blocks", "company_id")) {
            Schema::table("stocks_company_blocks", function (Blueprint $table) {
                $table->integer('company_id')->nullable()->default(null);
                $table->index('company_id');
            });
        }
        if (!Schema::hasColumn("stocks_company_blocks", "last_payment")) {
            Schema::table("stocks_company_blocks", function (Blueprint $table) {
                $table->integer('last_payment')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_company_blocks", "next_payment")) {
            Schema::table("stocks_company_blocks", function (Blueprint $table) {
                $table->integer('next_payment')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_company_blocks", "terminate")) {
            Schema::table("stocks_company_blocks", function (Blueprint $table) {
                $table->integer('terminate')->nullable()->default(null);
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
        Schema::dropIfExists('stocks_company_blocks');
    }
}
