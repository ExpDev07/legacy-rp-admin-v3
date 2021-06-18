<?php

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
        Schema::create('inventories', function (Blueprint $table) {
            $table->integer('id')->nullable(false)->autoIncrement();
            $table->string('item_name', 50)->nullable()->default(null);
            $table->longText('item_metadata')->nullable()->default(null);
            $table->string('inventory_name', 50)->nullable()->default(null);
            $table->integer('inventory_slot')->nullable()->default(null);
            $table->unique(['inventory_name']);
        });
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
