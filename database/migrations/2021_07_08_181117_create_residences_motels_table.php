<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResidencesMotelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("residences_motels")) {
            Schema::create('residences_motels', function (Blueprint $table) {
                $table->integer('id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("residences_motels", "motel")) {
            Schema::table("residences_motels", function (Blueprint $table) {
                $table->longText('motel')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("residences_motels", "room_id")) {
            Schema::table("residences_motels", function (Blueprint $table) {
                $table->integer('room_id')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("residences_motels", "cid")) {
            Schema::table("residences_motels", function (Blueprint $table) {
                $table->integer('cid')->nullable()->default(null);
                $table->index('cid');
            });
        }
        if (!Schema::hasColumn("residences_motels", "expire")) {
            Schema::table("residences_motels", function (Blueprint $table) {
                $table->integer('expire')->nullable()->default(null);
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
        Schema::dropIfExists('residences_motels');
    }
}
