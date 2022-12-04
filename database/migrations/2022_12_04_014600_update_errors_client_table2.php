<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateErrorsClientTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('errors_client', 'full_trace')) {
            Schema::table('errors_client', function (Blueprint $table) {
                $table->string('full_trace');
                $table->string('server_version');
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
            $table->dropColumn('full_trace');
            $table->dropColumn('server_version');
        });
    }
}
