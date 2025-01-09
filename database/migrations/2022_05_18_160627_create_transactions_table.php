<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transactable_type');
            $table->unsignedBigInteger('transactable_id');
            $table->decimal('debit',12,2)->nullable();
            $table->decimal('credit',12,2)->nullable();
            $table->decimal('balance',14,2);
            $table->string('particulars')->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->date('date')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
