<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGcphoneUsersContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gcphone_users_contacts', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('identifier', 60)->nullable();
            $table->string('number', 10)->nullable();
            $table->string('display', 64)->nullable()->default('-1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gcphone_users_contacts');
    }
}
