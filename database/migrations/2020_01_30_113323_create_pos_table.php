<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pos', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('estimate_id')->nullable();
            $table->unsignedInteger('customer_id')->nullable();
            $table->unsignedInteger('brand_id')->nullable();

            $table->date('sale_date')->nullable();
            $table->integer('sale_by')->nullable();
            $table->string('pos_number')->nullable();

            // Initial Data -> Actual Pricing of the added products
            $table->decimal('total',12,2)->default(0); //total product price
            $table->string('discount')->nullable(); //user input
            $table->decimal('actual_discount',12,2)->default(0); //calculated discount
            $table->decimal('receivable',12,2)->nullable(); //receivable after discount

            // Update on payment create/delete
            $table->decimal('paid',12,2)->default(0); //total paid

            // updates on new sell / return create/delete
            $table->decimal('returned',12,2)->default(0);//returned amount
            $table->decimal('final_receivable',12,2)->default(0);//after return -> receivable
            $table->decimal('due',12,2)->default(0); // updated due
            $table->decimal('total_purchase_cost', 12, 2)->nullable(); //updated after return
            $table->decimal('profit',10,2)->default(0);

            $table->text('note')->nullable();
            // $table->integer('courier_id');
            // $table->integer('delivery_method_id');
            // $table->decimal('delivery_cost');
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
        Schema::dropIfExists('pos');
    }
}
