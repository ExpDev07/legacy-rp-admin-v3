<?php

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
            $table->integer('id')->primary();
            $table->string('channel', 20)->nullable();
            $table->string('message')->nullable();
            $table->timestamp('time')->useCurrent();
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
