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
            $table->integer('blood_type');
            $table->longText('backstory');

            // Balance.
            $table->integer('cash')->default(0);
            $table->integer('bank')->default(0);
            $table->double('stocks_balance')->default(0);

            // Job.
            $table->string('job_name')->nullable();
            $table->string('department_name')->nullable();
            $table->string('position_name')->nullable();

            // Timestamps.
            $table->boolean('character_created')->nullable();
            $table->timestamp('character_creation_timestamp')->nullable();
            $table->boolean('character_deleted')->nullable();
            $table->timestamp('character_deletion_timestamp')->nullable();

            // Json.
            $table->json('coords')->nullable();
            $table->json('status_data')->nullable();
            $table->json('character_data')->nullable();
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
