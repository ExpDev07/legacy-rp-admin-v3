<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMdtReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mdt_reports', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('character_id')->nullable();
            $table->longText('character_name')->nullable();
            $table->longText('title')->nullable();
            $table->integer('timestamp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mdt_reports');
    }
}
