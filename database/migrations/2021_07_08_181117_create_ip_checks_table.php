<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIpChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("ip_checks")) {
            Schema::create('ip_checks', function (Blueprint $table) {
                $table->integer('id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("ip_checks", "ip_identifier")) {
            Schema::table("ip_checks", function (Blueprint $table) {
                $table->string('ip_identifier', 50)->nullable()->default(null);
                $table->index('ip_identifier');
            });
        }
        if (!Schema::hasColumn("ip_checks", "country_name")) {
            Schema::table("ip_checks", function (Blueprint $table) {
                $table->string('country_name', 50)->nullable()->default(null);
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
        Schema::dropIfExists('ip_checks');
    }
}
