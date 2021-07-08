<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("phones")) {
            Schema::create('phones', function (Blueprint $table) {
                $table->integer('phone_id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("phones", "sim_card_id")) {
            Schema::table("phones", function (Blueprint $table) {
                $table->string('sim_card_id', 50)->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("phones", "phone_passcode")) {
            Schema::table("phones", function (Blueprint $table) {
                $table->longText('phone_passcode')->nullable()->default(null);
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
        Schema::dropIfExists('phones');
    }
}
