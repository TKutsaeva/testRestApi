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
        Schema::create('category_item', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('item_id')->unsigned()->nullable(false);
            $table->bigInteger('category_id')->unsigned()->nullable(false);
            $table->timestamps();
        });
        Schema::table('category_item', function(Blueprint $table) {
            $table->foreign('item_id')->references('id')->on('items')->cascadeOnUpdate();
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnUpdate();
            $table->index(['item_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category_item');

    }
}
