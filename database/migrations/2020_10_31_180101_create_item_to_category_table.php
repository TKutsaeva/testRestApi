<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemToCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();
        Schema::create('item_to_category', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_item')->unsigned()->nullable(false);
            $table->bigInteger('id_category')->unsigned()->nullable(false);
            $table->index(['id_item', 'id_category']);
            $table->timestamps();
        });
        Schema::table('item_to_category', function(Blueprint $table) {
            $table->foreign('id_item')->references('id')->on('items')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('id_category')->references('id')->on('categories')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_to_category');

    }
}
