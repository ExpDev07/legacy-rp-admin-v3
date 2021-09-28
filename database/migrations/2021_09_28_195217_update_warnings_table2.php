<?php

use App\Warning;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class UpdateWarningsTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `warnings` MODIFY COLUMN `warning_type` ENUM('" . implode("', '", Warning::ValidTypes) . "') NOT NULL DEFAULT '" . Warning::TypeSystem . "'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `warnings` MODIFY COLUMN `warning_type` ENUM('note', 'warning') NOT NULL DEFAULT 'warning'");
    }
}
