<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlobalBanExceptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_ban_exceptions', function (Blueprint $table) {
            $table->integer('id')->nullable(false)->autoIncrement();
            $table->string('steam_identifier', 50)->nullable()->default(null);
            $table->index('steam_identifier');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('global_ban_exceptions');
    }
}