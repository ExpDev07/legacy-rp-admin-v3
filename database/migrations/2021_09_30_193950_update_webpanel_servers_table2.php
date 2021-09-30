<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class updateWebpanelServersTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn("webpanel_servers", "name")) {
            Schema::table('webpanel_servers', function (Blueprint $table) {
                $table->string('name')->nullable(true);
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
        Schema::table('webpanel_servers', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
}
