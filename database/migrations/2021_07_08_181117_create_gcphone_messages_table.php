<?php

use Illuminate\Support\Facades\DB;
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
        if (!Schema::hasTable("gcphone_messages")) {
            Schema::create('gcphone_messages', function (Blueprint $table) {
                $table->integer('id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("gcphone_messages", "transmitter")) {
            Schema::table("gcphone_messages", function (Blueprint $table) {
                $table->string('transmitter', 10)->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("gcphone_messages", "receiver")) {
            Schema::table("gcphone_messages", function (Blueprint $table) {
                $table->string('receiver', 10)->nullable()->default(null);
                $table->index('receiver');
            });
        }
        if (!Schema::hasColumn("gcphone_messages", "message")) {
            Schema::table("gcphone_messages", function (Blueprint $table) {
                $table->string('message', 255)->default('0');
            });
        }
        if (!Schema::hasColumn("gcphone_messages", "time")) {
            Schema::table("gcphone_messages", function (Blueprint $table) {
                $table->timestamp('time')->nullable(false)->useCurrent();
            });
        }
        if (!Schema::hasColumn("gcphone_messages", "isRead")) {
            Schema::table("gcphone_messages", function (Blueprint $table) {
                $table->integer('isRead')->default(0);
            });
        }
        if (!Schema::hasColumn("gcphone_messages", "owner")) {
            Schema::table("gcphone_messages", function (Blueprint $table) {
                $table->integer('owner')->default(0);
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
        Schema::dropIfExists('gcphone_messages');
    }
}
