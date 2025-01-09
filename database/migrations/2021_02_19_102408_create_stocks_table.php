<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('stockable_type');
            $table->integer('stockable_id');
            $table->bigInteger('purchase_id');
            $table->bigInteger('purchase_item_id');
            // In case of return -> Which stock_id it is returned from?
            $table->bigInteger('stock_id')->nullable();
            $table->integer('product_id');
            $table->integer('qty');
            // If return - IN - else - OUT
            $table->boolean('out')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stocks');
    }
}
