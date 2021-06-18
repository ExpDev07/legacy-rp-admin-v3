<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResidencesMotelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('residences_motels', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->longText('motel')->nullable();
            $table->integer('room_id')->nullable();
            $table->integer('cid')->nullable();
            $table->integer('expire')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('residences_motels');
    }
}
