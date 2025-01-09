<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            // $table->string('email')->nullable();
            $table->string('phone');
            $table->string('shop_name')->nullable();
            $table->string('shop_name_bangla')->nullable();
            $table->integer('address_id')->nullable();
            $table->integer('sr_id')->nullable();
            $table->integer('business_cat_id')->nullable();
            $table->decimal('opening_receivable', 12, 2)->nullable();
            $table->decimal('opening_payable', 12, 2)->nullable();
            // // calculated data
            $table->decimal('wallet_balance',14,2)->default(0);
            $table->decimal('total_receivable',20,2)->default(0);
            $table->decimal('total_payable',20,2)->default(0);
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
        Schema::dropIfExists('customers');
    }
}
