<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("inventories")) {
            Schema::create('inventories', function (Blueprint $table) {
                $table->integer('id')->nullable(false)->autoIncrement();
            });
        }
        if (!Schema::hasColumn("inventories", "item_name")) {
            Schema::table("inventories", function (Blueprint $table) {
                $table->string('item_name', 50)->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("inventories", "item_metadata")) {
            Schema::table("inventories", function (Blueprint $table) {
                $table->longText('item_metadata')->nullable()->default(null);
            });
        }
        if (!Schema::hasColumn("inventories", "inventory_name")) {
            Schema::table("inventories", function (Blueprint $table) {
                $table->string('inventory_name', 50)->nullable()->default(null);
                $table->index('inventory_name');
            });
        }
        if (!Schema::hasColumn("inventories", "inventory_slot")) {
            Schema::table("inventories", function (Blueprint $table) {
                $table->integer('inventory_slot')->nullable()->default(null);
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
        Schema::dropIfExists('inventories');
    }
}
