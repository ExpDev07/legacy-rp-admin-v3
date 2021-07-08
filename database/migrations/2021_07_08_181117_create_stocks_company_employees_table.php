<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStocksCompanyEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("stocks_company_employees")) {
            Schema::create('stocks_company_employees', function (Blueprint $table) {
                $table->integer('id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("stocks_company_employees", "company_id")) {
            Schema::table("stocks_company_employees", function (Blueprint $table) {
                $table->integer('company_id')->nullable()->default(null);
                $table->index('company_id');
            });
        }
        if (!Schema::hasColumn("stocks_company_employees", "employee_cid")) {
            Schema::table("stocks_company_employees", function (Blueprint $table) {
                $table->integer('employee_cid')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_company_employees", "employee_name")) {
            Schema::table("stocks_company_employees", function (Blueprint $table) {
                $table->longText('employee_name')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_company_employees", "position")) {
            Schema::table("stocks_company_employees", function (Blueprint $table) {
                $table->longText('position')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("stocks_company_employees", "salary")) {
            Schema::table("stocks_company_employees", function (Blueprint $table) {
                $table->integer('salary')->default(0);
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
        Schema::dropIfExists('stocks_company_employees');
    }
}
