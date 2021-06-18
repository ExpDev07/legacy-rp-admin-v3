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
            $table->integer('id')->nullable(false)->autoIncrement();
            $table->string('owner', 10)->nullable()->default(null);
            $table->string('num', 10)->nullable()->default(null);
            $table->integer('incoming')->nullable()->default(null);
            $table->timestamp('time')->nullable(false)->useCurrent();
            $table->integer('accepts')->nullable()->default(null);
            $table->unique(['owner']);
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
