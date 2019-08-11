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
            $table->bigIncrements('cid');
            $table->string('identifier');
            $table->integer('slot');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('gender');
            $table->integer('height');
            $table->string('dob');
            $table->text('story');
            $table->integer('cash');
            $table->integer('bank');
            $table->string('job')->nullable();

            // Json data values.
            $table->json('basicneeds');
            $table->json('licenses');
            $table->json('model');
            $table->json('tattoos');
            $table->json('ammo');
            $table->json('animations');
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
