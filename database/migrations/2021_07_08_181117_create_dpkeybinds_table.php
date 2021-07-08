<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDpkeybindsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("dpkeybinds")) {
            Schema::create('dpkeybinds', function (Blueprint $table) {
                $table->string('id', 50)->nullable(false)->primary();
            });
        }
        if (!Schema::hasColumn("dpkeybinds", "keybind1")) {
            Schema::table("dpkeybinds", function (Blueprint $table) {
                $table->string('keybind1', 50)->default('num4');
            });
        }
        if (!Schema::hasColumn("dpkeybinds", "emote1")) {
            Schema::table("dpkeybinds", function (Blueprint $table) {
                $table->string('emote1', 255)->default('');
            });
        }
        if (!Schema::hasColumn("dpkeybinds", "keybind2")) {
            Schema::table("dpkeybinds", function (Blueprint $table) {
                $table->string('keybind2', 50)->default('num5');
            });
        }
        if (!Schema::hasColumn("dpkeybinds", "emote2")) {
            Schema::table("dpkeybinds", function (Blueprint $table) {
                $table->string('emote2', 255)->default('');
            });
        }
        if (!Schema::hasColumn("dpkeybinds", "keybind3")) {
            Schema::table("dpkeybinds", function (Blueprint $table) {
                $table->string('keybind3', 50)->default('num6');
            });
        }
        if (!Schema::hasColumn("dpkeybinds", "emote3")) {
            Schema::table("dpkeybinds", function (Blueprint $table) {
                $table->string('emote3', 255)->default('');
            });
        }
        if (!Schema::hasColumn("dpkeybinds", "keybind4")) {
            Schema::table("dpkeybinds", function (Blueprint $table) {
                $table->string('keybind4', 50)->default('num7');
            });
        }
        if (!Schema::hasColumn("dpkeybinds", "emote4")) {
            Schema::table("dpkeybinds", function (Blueprint $table) {
                $table->string('emote4', 255)->default('');
            });
        }
        if (!Schema::hasColumn("dpkeybinds", "keybind5")) {
            Schema::table("dpkeybinds", function (Blueprint $table) {
                $table->string('keybind5', 50)->default('num8');
            });
        }
        if (!Schema::hasColumn("dpkeybinds", "emote5")) {
            Schema::table("dpkeybinds", function (Blueprint $table) {
                $table->string('emote5', 255)->default('');
            });
        }
        if (!Schema::hasColumn("dpkeybinds", "keybind6")) {
            Schema::table("dpkeybinds", function (Blueprint $table) {
                $table->string('keybind6', 50)->default('num9');
            });
        }
        if (!Schema::hasColumn("dpkeybinds", "emote6")) {
            Schema::table("dpkeybinds", function (Blueprint $table) {
                $table->string('emote6', 255)->default('');
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
        Schema::dropIfExists('dpkeybinds');
    }
}
