<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebpanelUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webpanel_users', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned()->nullable(false)->autoIncrement();
            $table->string('account_id', 255)->collation('utf8mb4_unicode_ci')->nullable(false);
            $table->string('name', 255)->collation('utf8mb4_unicode_ci')->nullable(false);
            $table->string('avatar', 255)->collation('utf8mb4_unicode_ci')->nullable(false);
            $table->string('remember_token', 100)->collation('utf8mb4_unicode_ci')->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webpanel_users');
    }
}
