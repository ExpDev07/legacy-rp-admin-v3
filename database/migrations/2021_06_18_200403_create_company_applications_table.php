<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_applications', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('app_applier_cid')->nullable();
            $table->longText('app_name')->nullable();
            $table->longText('app_description')->nullable();
            $table->longText('contact_person')->nullable();
            $table->longText('estimated_employees')->nullable();
            $table->longText('app_logo')->nullable();
            $table->longText('todays_date')->nullable();
            $table->longText('signature')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_applications');
    }
}
