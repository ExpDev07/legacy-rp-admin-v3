<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharactersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('characters', function (Blueprint $table) {
            $table->id('character_id');
            $table->string('steam_identifier');
            $table->integer('character_slot');
            $table->string('gender');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('date_of_birth');
            $table->integer('cash');
            $table->integer('bank');
            $table->string('job_name')->nullable();
            $table->string('job_department')->nullable();
            $table->string('job_position')->nullable();
            $table->mediumText('backstory')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('characters');
    }
}
