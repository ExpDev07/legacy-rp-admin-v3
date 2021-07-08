<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMdtWarrantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("mdt_warrants")) {
            Schema::create('mdt_warrants', function (Blueprint $table) {
                $table->integer('id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("mdt_warrants", "character_id")) {
            Schema::table("mdt_warrants", function (Blueprint $table) {
                $table->integer('character_id')->nullable()->default(null);
                $table->index('character_id');
            });
        }
        if (!Schema::hasColumn("mdt_warrants", "character_name")) {
            Schema::table("mdt_warrants", function (Blueprint $table) {
                $table->longText('character_name')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("mdt_warrants", "timestamp")) {
            Schema::table("mdt_warrants", function (Blueprint $table) {
                $table->integer('timestamp')->nullable()->default(null);
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
        Schema::dropIfExists('mdt_warrants');
    }
}
