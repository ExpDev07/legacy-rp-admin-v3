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
            $table->integer('cash')->default(0);
            $table->integer('bank')->default(0);
            $table->double('stocks_balance')->default(0);
            $table->string('job_name')->nullable();
            $table->string('department_name')->nullable();
            $table->string('position_name')->nullable();
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
