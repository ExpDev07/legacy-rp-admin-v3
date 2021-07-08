<?php

use Illuminate\Support\Facades\DB;
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
        if (!Schema::hasTable("stocks_companies")) {
            Schema::create('stocks_companies', function (Blueprint $table) {
                $table->integer('company_id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("stocks_companies", "owner_cid")) {
            Schema::table("stocks_companies", function (Blueprint $table) {
                $table->integer('owner_cid')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_companies", "owner_name")) {
            Schema::table("stocks_companies", function (Blueprint $table) {
                $table->longText('owner_name')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_companies", "company_name")) {
            Schema::table("stocks_companies", function (Blueprint $table) {
                $table->longText('company_name')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_companies", "company_description")) {
            Schema::table("stocks_companies", function (Blueprint $table) {
                $table->longText('company_description')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_companies", "company_logo")) {
            Schema::table("stocks_companies", function (Blueprint $table) {
                $table->longText('company_logo')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_companies", "company_balance")) {
            Schema::table("stocks_companies", function (Blueprint $table) {
                $table->double('company_balance')->default(0);
            });
        }
        if (!Schema::hasColumn("stocks_companies", "company_reg_timestamp")) {
            Schema::table("stocks_companies", function (Blueprint $table) {
                $table->timestamp('company_reg_timestamp')->nullable(false)->useCurrent();
            });
        }
        if (!Schema::hasColumn("stocks_companies", "total_shares")) {
            Schema::table("stocks_companies", function (Blueprint $table) {
                $table->integer('total_shares')->default(0);
            });
        }
        if (!Schema::hasColumn("stocks_companies", "total_shares_purchased")) {
            Schema::table("stocks_companies", function (Blueprint $table) {
                $table->integer('total_shares_purchased')->default(0);
            });
        }
        if (!Schema::hasColumn("stocks_companies", "max_shares")) {
            Schema::table("stocks_companies", function (Blueprint $table) {
                $table->integer('max_shares')->default(1000000);
            });
        }
        if (!Schema::hasColumn("stocks_companies", "share_price")) {
            Schema::table("stocks_companies", function (Blueprint $table) {
                $table->double('share_price')->default(0.01);
            });
        }
        if (!Schema::hasColumn("stocks_companies", "share_change")) {
            Schema::table("stocks_companies", function (Blueprint $table) {
                $table->double('share_change')->default(0);
            });
        }
        if (!Schema::hasColumn("stocks_companies", "bankrupt")) {
            Schema::table("stocks_companies", function (Blueprint $table) {
                $table->integer('bankrupt')->nullable()->default(null);
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
        Schema::dropIfExists('stocks_companies');
    }
}
