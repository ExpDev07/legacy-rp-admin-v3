<?php

use Illuminate\Support\Facades\DB;
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
        if (!Schema::hasTable("finances")) {
            Schema::create('finances', function (Blueprint $table) {
                $table->integer('id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("finances", "cid")) {
            Schema::table("finances", function (Blueprint $table) {
                $table->integer('cid')->nullable()->default(null);
                $table->index('cid');
            });
        }
        if (!Schema::hasColumn("finances", "amount")) {
            Schema::table("finances", function (Blueprint $table) {
                $table->integer('amount')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("finances", "completed")) {
            Schema::table("finances", function (Blueprint $table) {
                $table->integer('completed')->default(0);
            });
        }
        if (!Schema::hasColumn("finances", "timestamp")) {
            Schema::table("finances", function (Blueprint $table) {
                $table->timestamp('timestamp')->nullable(false)->useCurrent();
            });
        }
        if (!Schema::hasColumn("finances", "description")) {
            Schema::table("finances", function (Blueprint $table) {
                $table->longText('description')->nullable()->default(null);
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
        Schema::dropIfExists('finances');
    }
}
