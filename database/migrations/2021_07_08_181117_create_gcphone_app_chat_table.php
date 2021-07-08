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
        if (!Schema::hasTable("gcphone_app_chat")) {
            Schema::create('gcphone_app_chat', function (Blueprint $table) {
                $table->integer('id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("gcphone_app_chat", "channel")) {
            Schema::table("gcphone_app_chat", function (Blueprint $table) {
                $table->string('channel', 20)->nullable()->default(null);
                $table->index('channel');
            });
        }
        if (!Schema::hasColumn("gcphone_app_chat", "message")) {
            Schema::table("gcphone_app_chat", function (Blueprint $table) {
                $table->string('message', 255)->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("gcphone_app_chat", "time")) {
            Schema::table("gcphone_app_chat", function (Blueprint $table) {
                $table->timestamp('time')->nullable(false)->useCurrent();
            });
        }
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
