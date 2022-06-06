<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBlacklistedIdentifiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('blacklisted_identifiers', 'note')) {
            Schema::table('blacklisted_identifiers', function (Blueprint $table) {
                $table->string('note');
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
        Schema::table('blacklisted_identifiers', function (Blueprint $table) {
            $table->dropColumn('note');
        });
    }
}
