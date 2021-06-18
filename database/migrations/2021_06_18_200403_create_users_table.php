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
            $table->integer('user_id')->primary();
            $table->string('steam_identifier', 50)->nullable();
            $table->longText('player_name')->nullable();
            $table->longText('player_aliases')->nullable();
            $table->longText('identifiers')->nullable();
            $table->boolean('is_staff')->nullable()->default(0);
            $table->boolean('is_super_admin')->nullable()->default(0);
            $table->integer('playtime')->nullable()->default(0);
            $table->integer('last_connection')->nullable();
            $table->integer('total_joins')->nullable()->default(0);
            $table->integer('priority_level')->nullable();
            $table->longText('user_settings')->nullable();
            $table->longText('user_data')->nullable();
            $table->longText('activity_points')->nullable();
            $table->longText('staff_points')->nullable();
            $table->boolean('is_soft_banned')->nullable()->default(0);
            $table->boolean('is_trusted')->nullable()->default(0);
            $table->boolean('instant_join')->nullable()->default(0);
            $table->boolean('is_deprioritized')->nullable()->default(0);
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
