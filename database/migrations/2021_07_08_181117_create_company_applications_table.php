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
        if (!Schema::hasTable("company_applications")) {
            Schema::create('company_applications', function (Blueprint $table) {
                $table->integer('id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("company_applications", "app_applier_cid")) {
            Schema::table("company_applications", function (Blueprint $table) {
                $table->integer('app_applier_cid')->nullable()->default(null);
                $table->index('app_applier_cid');
            });
        }
        if (!Schema::hasColumn("company_applications", "app_name")) {
            Schema::table("company_applications", function (Blueprint $table) {
                $table->longText('app_name')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("company_applications", "app_description")) {
            Schema::table("company_applications", function (Blueprint $table) {
                $table->longText('app_description')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("company_applications", "contact_person")) {
            Schema::table("company_applications", function (Blueprint $table) {
                $table->longText('contact_person')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("company_applications", "estimated_employees")) {
            Schema::table("company_applications", function (Blueprint $table) {
                $table->longText('estimated_employees')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("company_applications", "app_logo")) {
            Schema::table("company_applications", function (Blueprint $table) {
                $table->longText('app_logo')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("company_applications", "todays_date")) {
            Schema::table("company_applications", function (Blueprint $table) {
                $table->longText('todays_date')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("company_applications", "signature")) {
            Schema::table("company_applications", function (Blueprint $table) {
                $table->longText('signature')->nullable()->default(null);
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
        Schema::dropIfExists('company_applications');
    }
}
