<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutfitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("outfits")) {
            Schema::create('outfits', function (Blueprint $table) {
                $table->integer('id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("outfits", "cid")) {
            Schema::table("outfits", function (Blueprint $table) {
                $table->integer('cid')->nullable()->default(null);
                $table->index('cid');
            });
        }
        if (!Schema::hasColumn("outfits", "name")) {
            Schema::table("outfits", function (Blueprint $table) {
                $table->longText('name')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("outfits", "data")) {
            Schema::table("outfits", function (Blueprint $table) {
                $table->longText('data')->nullable()->default(null);
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
        Schema::dropIfExists('outfits');
    }
}
