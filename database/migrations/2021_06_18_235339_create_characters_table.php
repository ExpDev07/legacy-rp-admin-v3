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
            $table->integer('character_id')->nullable(false)->autoIncrement();
            $table->string('steam_identifier', 50)->nullable()->default(null);
            $table->integer('character_slot')->nullable()->default(null);
            $table->integer('character_created')->default(0);
            $table->integer('character_deleted')->default(0);
            $table->longText('first_name')->nullable()->default(null);
            $table->longText('last_name')->nullable()->default(null);
            $table->longText('date_of_birth')->nullable()->default(null);
            $table->integer('gender')->nullable()->default(null);
            $table->longText('backstory')->nullable()->default(null);
            $table->integer('cash')->default(0);
            $table->integer('bank')->default(0);
            $table->integer('blood_type')->nullable()->default(null);
            $table->longText('coords')->nullable()->default(null);
            $table->longText('status_data')->nullable()->default(null);
            $table->longText('job_name')->nullable()->default(null);
            $table->longText('department_name')->nullable()->default(null);
            $table->longText('position_name')->nullable()->default(null);
            $table->longText('ammo_data')->nullable()->default(null);
            $table->longText('tattoos_data')->nullable()->default(null);
            $table->longText('phone_number')->nullable()->default(null);
            $table->tinyInteger('is_dead')->nullable()->default(null);
            $table->longText('model')->nullable()->default(null);
            $table->double('stocks_balance')->default(0);
            $table->integer('jail')->nullable()->default(null);
            $table->integer('character_creation_timestamp')->nullable()->default(null);
            $table->integer('character_deletion_timestamp')->nullable()->default(null);
            $table->longText('character_data')->nullable()->default(null);
            $table->unique(['steam_identifier']);
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
