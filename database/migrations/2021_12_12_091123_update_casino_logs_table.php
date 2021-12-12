<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCasinoLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('casino_logs', 'cycle_number')) {
            Schema::table('casino_logs', function (Blueprint $table) {
                $table->integer('money_won')->nullable(false)->default(0);
                $table->integer('bet_placed')->nullable(false)->default(0);
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
        Schema::table('casino_logs', function (Blueprint $table) {
            $table->dropColumn('money_won');
            $table->dropColumn('bet_placed');
        });
    }
}
