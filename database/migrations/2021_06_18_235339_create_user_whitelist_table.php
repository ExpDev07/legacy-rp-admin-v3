<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserWhitelistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_whitelist', function (Blueprint $table) {
            $table->integer('whitelist_id')->nullable(false)->autoIncrement();
            $table->string('steam_identifier', 50)->nullable()->default(null);
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
        Schema::dropIfExists('user_whitelist');
    }
}
