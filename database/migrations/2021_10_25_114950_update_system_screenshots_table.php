<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSystemScreenshotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('system_screenshots', 'created_at')) {
            Schema::table('system_screenshots', function (Blueprint $table) {
                $table->integer('created_at')->nullable(false)->default(0);
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
        Schema::table('system_screenshots', function (Blueprint $table) {
            $table->dropColumn('created_at');
        });
    }
}
