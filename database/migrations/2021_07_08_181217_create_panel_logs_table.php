<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePanelLogsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("panel_logs")) {
            Schema::create('panel_logs', function (Blueprint $table) {
                $table->integer('id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn('panel_logs', 'timestamp')) {
            Schema::table('panel_logs', function (Blueprint $table) {
                $table->timestamp('timestamp')->useCurrent();
            });
        }
        if (!Schema::hasColumn('panel_logs', 'source_identifier')) {
            Schema::table('panel_logs', function (Blueprint $table) {
                $table->string('source_identifier');
            });
        }
        if (!Schema::hasColumn('panel_logs', 'target_identifier')) {
            Schema::table('panel_logs', function (Blueprint $table) {
                $table->string('target_identifier');
            });
        }
        if (!Schema::hasColumn('panel_logs', 'log')) {
            Schema::table('panel_logs', function (Blueprint $table) {
                $table->string('log');
            });
        }
        if (!Schema::hasColumn('panel_logs', 'action')) {
            Schema::table('panel_logs', function (Blueprint $table) {
                $table->string('action');
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
        Schema::table('panel_logs', function (Blueprint $table) {
            $table->dropColumn('action');
        });
    }

}
