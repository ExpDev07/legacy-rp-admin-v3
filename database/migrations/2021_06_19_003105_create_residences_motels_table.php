<?php

use Illuminate\Support\Facades\DB;
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
            $table->integer('id')->nullable(false)->autoIncrement();
            $table->longText('motel')->nullable()->default(null);
            $table->integer('room_id')->nullable()->default(null);
            $table->integer('cid')->nullable()->default(null);
            $table->integer('expire')->nullable()->default(null);
            $table->index('cid');
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
