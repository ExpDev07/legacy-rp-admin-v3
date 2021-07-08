<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGcphoneCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("gcphone_calls")) {
            Schema::create('gcphone_calls', function (Blueprint $table) {
                $table->integer('id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("gcphone_calls", "owner")) {
            Schema::table("gcphone_calls", function (Blueprint $table) {
                $table->string('owner', 10)->nullable()->default(null);
                $table->index('owner');
            });
        }
        if (!Schema::hasColumn("gcphone_calls", "num")) {
            Schema::table("gcphone_calls", function (Blueprint $table) {
                $table->string('num', 10)->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("gcphone_calls", "incoming")) {
            Schema::table("gcphone_calls", function (Blueprint $table) {
                $table->integer('incoming')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("gcphone_calls", "time")) {
            Schema::table("gcphone_calls", function (Blueprint $table) {
                $table->timestamp('time')->nullable(false)->useCurrent();
            });
        }
        if (!Schema::hasColumn("gcphone_calls", "accepts")) {
            Schema::table("gcphone_calls", function (Blueprint $table) {
                $table->integer('accepts')->nullable()->default(null);
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
        Schema::dropIfExists('gcphone_calls');
    }
}
