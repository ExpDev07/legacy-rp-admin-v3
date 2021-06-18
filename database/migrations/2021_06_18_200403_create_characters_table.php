<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->integer('character_id')->primary();
            $table->string('steam_identifier', 50)->nullable();
            $table->integer('character_slot')->nullable();
            $table->integer('character_created')->nullable()->default(0);
            $table->integer('character_deleted')->nullable()->default(0);
            $table->longText('first_name')->nullable();
            $table->longText('last_name')->nullable();
            $table->longText('date_of_birth')->nullable();
            $table->integer('gender')->nullable();
            $table->longText('backstory')->nullable();
            $table->integer('cash')->nullable()->default(0);
            $table->integer('bank')->nullable()->default(0);
            $table->integer('blood_type')->nullable();
            $table->longText('coords')->nullable();
            $table->longText('status_data')->nullable();
            $table->longText('job_name')->nullable();
            $table->longText('department_name')->nullable();
            $table->longText('position_name')->nullable();
            $table->longText('ammo_data')->nullable();
            $table->longText('tattoos_data')->nullable();
            $table->longText('phone_number')->nullable();
            $table->boolean('is_dead')->nullable();
            $table->longText('model')->nullable();
            $table->double('stocks_balance')->nullable()->default(0);
            $table->integer('jail')->nullable();
            $table->integer('character_creation_timestamp')->nullable();
            $table->integer('character_deletion_timestamp')->nullable();
            $table->longText('character_data')->nullable();
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
