<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserBansTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('user_bans', 'creator_identifier')) {
            Schema::table('user_bans', function (Blueprint $table) {
                $table->string('creator_identifier');
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
        Schema::table('user_bans', function (Blueprint $table) {
            $table->dropColumn('creator_identifier');
        });
    }

}
