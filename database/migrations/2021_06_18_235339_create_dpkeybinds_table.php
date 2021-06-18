<?php

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
        Schema::create('dpkeybinds', function (Blueprint $table) {
            $table->string('id', 50)->nullable(false)->primary();
            $table->string('keybind1', 50)->default('num4');
            $table->string('emote1', 255)->default('');
            $table->string('keybind2', 50)->default('num5');
            $table->string('emote2', 255)->default('');
            $table->string('keybind3', 50)->default('num6');
            $table->string('emote3', 255)->default('');
            $table->string('keybind4', 50)->default('num7');
            $table->string('emote4', 255)->default('');
            $table->string('keybind5', 50)->default('num8');
            $table->string('emote5', 255)->default('');
            $table->string('keybind6', 50)->default('num9');
            $table->string('emote6', 255)->default('');
            $table->unique(['id']);
        });
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
