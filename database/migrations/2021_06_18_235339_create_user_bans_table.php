<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserBansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_bans', function (Blueprint $table) {
            $table->integer('id')->nullable(false)->autoIncrement();
            $table->string('ban_hash', 50)->nullable()->default(null);
            $table->string('identifier', 50)->nullable()->default(null);
            $table->string('smurf_account', 50)->nullable()->default(null);
            $table->longText('creator_name')->nullable()->default(null);
            $table->longText('reason')->nullable()->default(null);
            $table->integer('timestamp')->nullable()->default(null);
            $table->integer('expire')->nullable()->default(null);
            $table->unique(['identifier']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_bans');
    }
}
