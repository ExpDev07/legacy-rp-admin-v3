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
        Schema::create('stocks_company_employees', function (Blueprint $table) {
            $table->integer('id')->nullable(false)->autoIncrement();
            $table->integer('company_id')->nullable()->default(null);
            $table->integer('employee_cid')->nullable()->default(null);
            $table->longText('employee_name')->nullable()->default(null);
            $table->longText('position')->nullable()->default(null);
            $table->integer('salary')->default(0);
            $table->index('company_id');
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
