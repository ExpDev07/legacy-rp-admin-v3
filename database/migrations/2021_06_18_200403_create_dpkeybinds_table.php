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
            $table->string('id', 50)->primary();
            $table->string('keybind1', 50)->nullable()->default('num4');
            $table->string('emote1')->nullable()->default('');
            $table->string('keybind2', 50)->nullable()->default('num5');
            $table->string('emote2')->nullable()->default('');
            $table->string('keybind3', 50)->nullable()->default('num6');
            $table->string('emote3')->nullable()->default('');
            $table->string('keybind4', 50)->nullable()->default('num7');
            $table->string('emote4')->nullable()->default('');
            $table->string('keybind5', 50)->nullable()->default('num8');
            $table->string('emote5')->nullable()->default('');
            $table->string('keybind6', 50)->nullable()->default('num9');
            $table->string('emote6')->nullable()->default('');
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
