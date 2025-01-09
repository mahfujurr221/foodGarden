<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_returns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('pos_id');
            $table->integer('customer_id');
            // Sell value of product - Being returned ->Substracting DISCOUNT***
            $table->decimal('return_product_value', 12, 2); //value of returned product after substracting discount
            $table->decimal('calculated_discount', 10, 2);


            // Considering "Previous Returns", "Discount", "SELECTED PRODUCTS"
            // $table->decimal('should_pay', 12, 2);


            // Seller/Admin Decided T\o Pay
            // $table->decimal('payable_to_customer', 12, 2);
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
        Schema::dropIfExists('order_returns');
    }
}
