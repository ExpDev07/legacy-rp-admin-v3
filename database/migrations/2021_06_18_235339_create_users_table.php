<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->integer('user_id')->nullable(false)->autoIncrement();
            $table->string('steam_identifier', 50)->nullable()->default(null);
            $table->longText('player_name')->nullable()->default(null);
            $table->longText('player_aliases')->nullable()->default(null);
            $table->longText('identifiers')->nullable()->default(null);
            $table->tinyInteger('is_staff')->default(0);
            $table->tinyInteger('is_super_admin')->default(0);
            $table->integer('playtime')->default(0);
            $table->integer('last_connection')->nullable()->default(null);
            $table->integer('total_joins')->default(0);
            $table->integer('priority_level')->nullable()->default(null);
            $table->longText('user_settings')->nullable()->default(null);
            $table->longText('user_data')->nullable()->default(null);
            $table->longText('activity_points')->nullable()->default(null);
            $table->longText('staff_points')->nullable()->default(null);
            $table->tinyInteger('is_soft_banned')->default(0);
            $table->tinyInteger('is_trusted')->default(0);
            $table->tinyInteger('instant_join')->default(0);
            $table->tinyInteger('is_deprioritized')->default(0);
            $table->unique(['steam_identifier']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
