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
        Schema::create('ip_checks', function (Blueprint $table) {
            $table->integer('id')->nullable(false)->autoIncrement();
            $table->string('ip_identifier', 50)->nullable()->default(null);
            $table->string('country_name', 50)->nullable()->default(null);
            $table->index('ip_identifier');
        });
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
