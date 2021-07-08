<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStocksCompanyPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("stocks_company_properties")) {
            Schema::create('stocks_company_properties', function (Blueprint $table) {
                $table->integer('id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("stocks_company_properties", "company_id")) {
            Schema::table("stocks_company_properties", function (Blueprint $table) {
                $table->integer('company_id')->nullable()->default(null);
                $table->index('company_id');
            });
        }
        if (!Schema::hasColumn("stocks_company_properties", "block_id")) {
            Schema::table("stocks_company_properties", function (Blueprint $table) {
                $table->integer('block_id')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_company_properties", "property_id")) {
            Schema::table("stocks_company_properties", function (Blueprint $table) {
                $table->integer('property_id')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_company_properties", "property_name")) {
            Schema::table("stocks_company_properties", function (Blueprint $table) {
                $table->longText('property_name')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_company_properties", "property_type")) {
            Schema::table("stocks_company_properties", function (Blueprint $table) {
                $table->tinyInteger('property_type')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_company_properties", "property_address")) {
            Schema::table("stocks_company_properties", function (Blueprint $table) {
                $table->longText('property_address')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_company_properties", "property_cost")) {
            Schema::table("stocks_company_properties", function (Blueprint $table) {
                $table->integer('property_cost')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_company_properties", "property_renter")) {
            Schema::table("stocks_company_properties", function (Blueprint $table) {
                $table->longText('property_renter')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_company_properties", "property_renter_cid")) {
            Schema::table("stocks_company_properties", function (Blueprint $table) {
                $table->integer('property_renter_cid')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_company_properties", "property_income")) {
            Schema::table("stocks_company_properties", function (Blueprint $table) {
                $table->integer('property_income')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_company_properties", "property_last_pay")) {
            Schema::table("stocks_company_properties", function (Blueprint $table) {
                $table->integer('property_last_pay')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_company_properties", "shared_keys")) {
            Schema::table("stocks_company_properties", function (Blueprint $table) {
                $table->longText('shared_keys')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_company_properties", "furniture_enabled")) {
            Schema::table("stocks_company_properties", function (Blueprint $table) {
                $table->tinyInteger('furniture_enabled')->default(1);
            });
        }
        if (!Schema::hasColumn("stocks_company_properties", "furniture_last_pay")) {
            Schema::table("stocks_company_properties", function (Blueprint $table) {
                $table->integer('furniture_last_pay')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_company_properties", "terminate")) {
            Schema::table("stocks_company_properties", function (Blueprint $table) {
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
        Schema::dropIfExists('stocks_company_properties');
    }
}
