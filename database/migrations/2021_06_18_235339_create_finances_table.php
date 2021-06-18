<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finances', function (Blueprint $table) {
            $table->integer('id')->nullable(false)->autoIncrement();
            $table->integer('cid')->nullable()->default(null);
            $table->integer('amount')->nullable()->default(null);
            $table->integer('completed')->default(0);
            $table->timestamp('timestamp')->nullable(false)->useCurrent();
            $table->longText('description')->nullable()->default(null);
            $table->unique(['cid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('finances');
    }
}
