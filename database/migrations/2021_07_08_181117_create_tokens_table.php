<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("tokens")) {
            Schema::create('tokens', function (Blueprint $table) {
                $table->integer('token_id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("tokens", "token")) {
            Schema::table("tokens", function (Blueprint $table) {
                $table->longText('token')->nullable()->default(null);
                $table->index('token');
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
        Schema::dropIfExists('tokens');
    }
}
