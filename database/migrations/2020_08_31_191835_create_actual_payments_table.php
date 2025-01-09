<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActualPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actual_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('wallet_payment')->default(0)->nullable();
            $table->enum('payment_type', ['receive', 'pay']);

            $table->integer('customer_id')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->decimal('previous_due',14,2)->default(0);
            $table->decimal('amount',12,2);
            $table->decimal('due',14,2)->default(0);
            $table->date('date')->nullable();
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
        Schema::dropIfExists('actual_payments');
    }
}
