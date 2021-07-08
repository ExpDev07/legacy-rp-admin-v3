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
        if (!Schema::hasTable("gcphone_users_contacts")) {
            Schema::create('gcphone_users_contacts', function (Blueprint $table) {
                $table->integer('id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("gcphone_users_contacts", "identifier")) {
            Schema::table("gcphone_users_contacts", function (Blueprint $table) {
                $table->string('identifier', 60)->nullable()->default(null);
                $table->index('identifier');
            });
        }
        if (!Schema::hasColumn("gcphone_users_contacts", "number")) {
            Schema::table("gcphone_users_contacts", function (Blueprint $table) {
                $table->string('number', 10)->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("gcphone_users_contacts", "display")) {
            Schema::table("gcphone_users_contacts", function (Blueprint $table) {
                $table->string('display', 64)->default('-1');
            });
        }
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
