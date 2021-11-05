<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateErrorsClientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('errors_client', 'cycle_number')) {
            Schema::table('errors_client', function (Blueprint $table) {
                $table->integer('cycle_number')->nullable(false)->default(0);
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
        Schema::table('errors_client', function (Blueprint $table) {
            $table->dropColumn('cycle_number');
        });
    }
}
