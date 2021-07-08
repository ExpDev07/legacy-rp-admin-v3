<?php

use Illuminate\Support\Facades\DB;
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
        if (!Schema::hasTable("characters")) {
            Schema::create('characters', function (Blueprint $table) {
                $table->integer('character_id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("characters", "steam_identifier")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->string('steam_identifier', 50)->nullable()->default(null);
                $table->index('steam_identifier');
            });
        }
        if (!Schema::hasColumn("characters", "character_slot")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->integer('character_slot')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("characters", "character_created")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->integer('character_created')->default(0);
            });
        }
        if (!Schema::hasColumn("characters", "character_deleted")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->integer('character_deleted')->default(0);
            });
        }
        if (!Schema::hasColumn("characters", "first_name")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->longText('first_name')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("characters", "last_name")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->longText('last_name')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("characters", "date_of_birth")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->longText('date_of_birth')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("characters", "gender")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->integer('gender')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("characters", "backstory")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->longText('backstory')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("characters", "cash")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->integer('cash')->default(0);
            });
        }
        if (!Schema::hasColumn("characters", "bank")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->integer('bank')->default(0);
            });
        }
        if (!Schema::hasColumn("characters", "blood_type")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->integer('blood_type')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("characters", "coords")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->longText('coords')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("characters", "status_data")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->longText('status_data')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("characters", "job_name")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->longText('job_name')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("characters", "department_name")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->longText('department_name')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("characters", "position_name")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->longText('position_name')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("characters", "ammo_data")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->longText('ammo_data')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("characters", "tattoos_data")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->longText('tattoos_data')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("characters", "phone_number")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->longText('phone_number')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("characters", "is_dead")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->tinyInteger('is_dead')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("characters", "model")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->longText('model')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("characters", "stocks_balance")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->double('stocks_balance')->default(0);
            });
        }
        if (!Schema::hasColumn("characters", "jail")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->integer('jail')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("characters", "character_creation_timestamp")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->integer('character_creation_timestamp')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("characters", "character_deletion_timestamp")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->integer('character_deletion_timestamp')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("characters", "character_data")) {
            Schema::table("characters", function (Blueprint $table) {
                $table->longText('character_data')->nullable()->default(null);
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
        Schema::dropIfExists('characters');
    }
}
