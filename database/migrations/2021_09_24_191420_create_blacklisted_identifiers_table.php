<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlacklistedIdentifiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("blacklisted_identifiers")) {
            Schema::create('blacklisted_identifiers', function (Blueprint $table) {
                $table->integer('blacklist_id')->unsigned()->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("blacklisted_identifiers", "identifier")) {
            Schema::table("blacklisted_identifiers", function (Blueprint $table) {
                $table->longText('identifier');
            });
        }
        if (!Schema::hasColumn("blacklisted_identifiers", "creator_identifier")) {
            Schema::table("blacklisted_identifiers", function (Blueprint $table) {
                $table->longText('creator_identifier');
            });
        }
        if (!Schema::hasColumn("blacklisted_identifiers", "reason")) {
            Schema::table("blacklisted_identifiers", function (Blueprint $table) {
                $table->longText('reason');
            });
        }
        if (!Schema::hasColumn("blacklisted_identifiers", "timestamp")) {
            Schema::table("blacklisted_identifiers", function (Blueprint $table) {
                $table->timestamp('timestamp')->useCurrent();
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
        Schema::dropIfExists('blacklisted_identifiers');
    }
}
