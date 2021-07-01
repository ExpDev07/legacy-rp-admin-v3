<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGcphoneAppChatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gcphone_app_chat', function (Blueprint $table) {
            $table->integer('id')->nullable(false)->autoIncrement();
            $table->string('channel', 20)->nullable()->default(null);
            $table->string('message', 255)->nullable()->default(null);
            $table->timestamp('time')->nullable(false)->useCurrent();
            $table->index('channel');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gcphone_app_chat');
    }
}
