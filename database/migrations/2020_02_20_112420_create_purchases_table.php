<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedInteger('supplier_id')->nullable();

            $table->date('purchase_date');
            $table->decimal('payable', 12, 2);
            $table->decimal('paid',12,2)->default(0);
            $table->decimal('due',12,2)->default(0);
            $table->decimal('carrying_cost', 10, 2)->nullable();
            $table->text('note')->nullable();
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
        Schema::dropIfExists('purchases');
    }
}
