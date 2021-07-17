<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePanelLogsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('panel_logs', 'log')) {
            Schema::table('panel_logs', function (Blueprint $table) {
                $table->longText('log')->change();
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
        if (Schema::hasColumn('panel_logs', 'log')) {
            Schema::table('panel_logs', function (Blueprint $table) {
                $table->string('log')->change();
            });
        }
    }

}
