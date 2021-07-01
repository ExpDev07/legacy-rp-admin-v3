<?php

use Illuminate\Support\Facades\DB;
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
            $table->integer('id')->nullable(false)->autoIncrement();
            $table->integer('app_applier_cid')->nullable()->default(null);
            $table->longText('app_name')->nullable()->default(null);
            $table->longText('app_description')->nullable()->default(null);
            $table->longText('contact_person')->nullable()->default(null);
            $table->longText('estimated_employees')->nullable()->default(null);
            $table->longText('app_logo')->nullable()->default(null);
            $table->longText('todays_date')->nullable()->default(null);
            $table->longText('signature')->nullable()->default(null);
            $table->index('app_applier_cid');
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
