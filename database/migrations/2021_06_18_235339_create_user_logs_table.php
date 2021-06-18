<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_logs', function (Blueprint $table) {
            $table->integer('id')->nullable(false)->autoIncrement();
            $table->string('identifier', 50)->nullable()->default(null);
            $table->string('action', 50)->nullable()->default(null);
            $table->longText('details')->nullable()->default(null);
            $table->longText('metadata')->nullable()->default(null);
            $table->timestamp('timestamp')->nullable(false)->useCurrent();
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
        Schema::dropIfExists('user_logs');
    }
}
