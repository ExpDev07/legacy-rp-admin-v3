<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePanelLogSearchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		if (!Schema::hasColumn('panel_log_searches', 'identifier')) {
            Schema::table('panel_log_searches', function (Blueprint $table) {
                $table->string('identifier')->nullable();
                $table->string('server')->nullable();
                $table->integer('page')->nullable();
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
        Schema::table('panel_log_searches', function (Blueprint $table) {
            $table->dropColumn('identifier');
            $table->dropColumn('server');
            $table->dropColumn('page');
        });
    }
}
