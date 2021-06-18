<?php

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
        Schema::create('gcphone_calls', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('owner', 10)->nullable();
            $table->string('num', 10)->nullable();
            $table->integer('incoming')->nullable();
            $table->timestamp('time')->useCurrent();
            $table->integer('accepts')->nullable();
        });
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
