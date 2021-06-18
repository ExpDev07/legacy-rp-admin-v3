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
            $table->integer('id')->primary();
            $table->string('transmitter', 10)->nullable();
            $table->string('receiver', 10)->nullable();
            $table->string('message')->nullable()->default('0');
            $table->timestamp('time')->useCurrent();
            $table->integer('isRead')->nullable()->default(0);
            $table->integer('owner')->nullable()->default(0);
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
