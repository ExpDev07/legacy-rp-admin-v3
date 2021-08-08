<?php

use App\Warning;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateWarningsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('warnings', 'warning_type')) {
            Schema::table('warnings', function (Blueprint $table) {
                $table->enum('warning_type', Warning::ValidTypes)->default(Warning::TypeWarning);
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
        Schema::table('warnings', function (Blueprint $table) {
            $table->dropColumn('warning_type');
        });
    }

}
