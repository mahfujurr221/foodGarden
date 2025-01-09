<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'payments', function ( Blueprint $table ) {
            $table->bigIncrements( 'id' );
            // $table->string('transaction_id');
            $table->integer( 'actual_payment_id' );
            $table->integer('bank_account_id')->nullable();

            $table->boolean('wallet_payment')->default(0)->nullable();

            $table->date( 'payment_date' )->nullable();
            $table->enum( 'payment_type', [ 'receive', 'pay' ] );
            $table->unsignedInteger( 'paymentable_id' );
            $table->string( 'paymentable_type' );
            $table->decimal( 'pay_amount',12,2 )->nullable();
            $table->decimal( 'discount',8,2 )->nullable();
            $table->integer( 'method' )->nullable();
            // $table->text( 'note' )->nullable();
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'payments' );
    }
}
