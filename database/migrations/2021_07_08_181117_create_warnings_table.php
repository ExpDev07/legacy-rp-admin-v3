<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWarningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("warnings")) {
            Schema::create('warnings', function (Blueprint $table) {
                $table->bigInteger('id')->unsigned()->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("warnings", "player_id")) {
            Schema::table("warnings", function (Blueprint $table) {
                $table->bigInteger('player_id')->unsigned()->nullable(false);
            });
        }
        if (!Schema::hasColumn("warnings", "issuer_id")) {
            Schema::table("warnings", function (Blueprint $table) {
                $table->bigInteger('issuer_id')->unsigned()->nullable(false);
            });
        }
        if (!Schema::hasColumn("warnings", "message")) {
            Schema::table("warnings", function (Blueprint $table) {
                $table->text('message')->collation('utf8mb4_unicode_ci')->nullable(false);
            });
        }
        if (!Schema::hasColumn("warnings", "created_at")) {
            Schema::table("warnings", function (Blueprint $table) {
                $table->timestamp('created_at')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("warnings", "updated_at")) {
            Schema::table("warnings", function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable()->default(null);
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
        Schema::dropIfExists('warnings');
    }
}
