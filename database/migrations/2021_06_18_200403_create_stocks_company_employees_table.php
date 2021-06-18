<?php

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
        Schema::create('stocks_company_employees', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('company_id')->nullable();
            $table->integer('employee_cid')->nullable();
            $table->longText('employee_name')->nullable();
            $table->longText('position')->nullable();
            $table->integer('salary')->nullable()->default(0);
        });
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
