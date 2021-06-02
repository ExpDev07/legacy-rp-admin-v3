<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateCharactersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('characters', function (Blueprint $table) {
            DB::statement('ALTER TABLE characters CHANGE gender gender_old VARCHAR(255);');
            DB::statement('ALTER TABLE characters ADD gender INT(11) DEFAULT NULL AFTER gender_old;');

            DB::statement('UPDATE characters SET gender=0 WHERE gender_old="male";');
            DB::statement('UPDATE characters SET gender=1 WHERE gender_old="female";');

            $table->dropColumn('gender_old');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('characters', function (Blueprint $table) {
            DB::statement('ALTER TABLE characters CHANGE gender gender_old INT(11);');
            DB::statement('ALTER TABLE characters ADD gender VARCHAR(255) DEFAULT NULL AFTER gender_old;');

            DB::statement('UPDATE characters SET gender="male" WHERE gender_old=0;');
            DB::statement('UPDATE characters SET gender="female" WHERE gender_old=1;');

            $table->dropColumn('gender_old');
        });
    }

}
