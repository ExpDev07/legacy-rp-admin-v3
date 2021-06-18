<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGcphoneMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gcphone_messages', function (Blueprint $table) {
            $table->integer('id')->nullable(false)->autoIncrement();
            $table->string('transmitter', 10)->nullable()->default(null);
            $table->string('receiver', 10)->nullable()->default(null);
            $table->string('message', 255)->default('0');
            $table->timestamp('time')->nullable(false)->useCurrent();
            $table->integer('isRead')->default(0);
            $table->integer('owner')->default(0);
            $table->unique(['receiver']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gcphone_messages');
    }
}
