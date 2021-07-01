<?php

use Illuminate\Support\Facades\DB;
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
            $table->integer('id')->nullable(false)->autoIncrement();
            $table->string('identifier', 60)->nullable()->default(null);
            $table->string('number', 10)->nullable()->default(null);
            $table->string('display', 64)->default('-1');
            $table->index('identifier');
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
