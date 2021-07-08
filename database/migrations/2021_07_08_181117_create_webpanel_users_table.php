<?php

use Illuminate\Support\Facades\DB;
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
        if (!Schema::hasTable("webpanel_users")) {
            Schema::create('webpanel_users', function (Blueprint $table) {
                $table->bigInteger('id')->unsigned()->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("webpanel_users", "account_id")) {
            Schema::table("webpanel_users", function (Blueprint $table) {
                $table->string('account_id', 255)->collation('utf8mb4_unicode_ci')->nullable(false);
            });
        }
        if (!Schema::hasColumn("webpanel_users", "name")) {
            Schema::table("webpanel_users", function (Blueprint $table) {
                $table->string('name', 255)->collation('utf8mb4_unicode_ci')->nullable(false);
            });
        }
        if (!Schema::hasColumn("webpanel_users", "avatar")) {
            Schema::table("webpanel_users", function (Blueprint $table) {
                $table->string('avatar', 255)->collation('utf8mb4_unicode_ci')->nullable(false);
            });
        }
        if (!Schema::hasColumn("webpanel_users", "remember_token")) {
            Schema::table("webpanel_users", function (Blueprint $table) {
                $table->string('remember_token', 100)->collation('utf8mb4_unicode_ci')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("webpanel_users", "created_at")) {
            Schema::table("webpanel_users", function (Blueprint $table) {
                $table->timestamp('created_at')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("webpanel_users", "updated_at")) {
            Schema::table("webpanel_users", function (Blueprint $table) {
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
        Schema::dropIfExists('webpanel_users');
    }
}
