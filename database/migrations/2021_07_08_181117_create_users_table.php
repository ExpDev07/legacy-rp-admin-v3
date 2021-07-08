<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("users")) {
            Schema::create('users', function (Blueprint $table) {
                $table->integer('user_id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("users", "steam_identifier")) {
            Schema::table("users", function (Blueprint $table) {
                $table->string('steam_identifier', 50)->nullable()->default(null);
                $table->index('steam_identifier');
            });
        }
        if (!Schema::hasColumn("users", "player_name")) {
            Schema::table("users", function (Blueprint $table) {
                $table->longText('player_name')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("users", "player_aliases")) {
            Schema::table("users", function (Blueprint $table) {
                $table->longText('player_aliases')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("users", "identifiers")) {
            Schema::table("users", function (Blueprint $table) {
                $table->longText('identifiers')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("users", "is_staff")) {
            Schema::table("users", function (Blueprint $table) {
                $table->tinyInteger('is_staff')->default(0);
            });
        }
        if (!Schema::hasColumn("users", "is_super_admin")) {
            Schema::table("users", function (Blueprint $table) {
                $table->tinyInteger('is_super_admin')->default(0);
            });
        }
        if (!Schema::hasColumn("users", "playtime")) {
            Schema::table("users", function (Blueprint $table) {
                $table->integer('playtime')->default(0);
            });
        }
        if (!Schema::hasColumn("users", "last_connection")) {
            Schema::table("users", function (Blueprint $table) {
                $table->integer('last_connection')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("users", "total_joins")) {
            Schema::table("users", function (Blueprint $table) {
                $table->integer('total_joins')->default(0);
            });
        }
        if (!Schema::hasColumn("users", "priority_level")) {
            Schema::table("users", function (Blueprint $table) {
                $table->integer('priority_level')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("users", "user_settings")) {
            Schema::table("users", function (Blueprint $table) {
                $table->longText('user_settings')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("users", "user_data")) {
            Schema::table("users", function (Blueprint $table) {
                $table->longText('user_data')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("users", "activity_points")) {
            Schema::table("users", function (Blueprint $table) {
                $table->longText('activity_points')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("users", "staff_points")) {
            Schema::table("users", function (Blueprint $table) {
                $table->longText('staff_points')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("users", "is_soft_banned")) {
            Schema::table("users", function (Blueprint $table) {
                $table->tinyInteger('is_soft_banned')->default(0);
            });
        }
        if (!Schema::hasColumn("users", "is_trusted")) {
            Schema::table("users", function (Blueprint $table) {
                $table->tinyInteger('is_trusted')->default(0);
            });
        }
        if (!Schema::hasColumn("users", "instant_join")) {
            Schema::table("users", function (Blueprint $table) {
                $table->tinyInteger('instant_join')->default(0);
            });
        }
        if (!Schema::hasColumn("users", "is_deprioritized")) {
            Schema::table("users", function (Blueprint $table) {
                $table->tinyInteger('is_deprioritized')->default(0);
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
        Schema::dropIfExists('users');
    }
}
