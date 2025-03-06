<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->text('address')->nullable();
            $table->decimal('opening_receivable', 12, 2)->nullable();
            $table->decimal('opening_payable', 12, 2)->nullable();
            $table->boolean('default')->default(0);

            // calculated
            $table->decimal('wallet_balance',14,2)->default(0);
            $table->decimal('total_receivable',20,2)->default(0);
            $table->decimal('total_payable',20,2)->default(0);
            //status
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('suppliers');
    }
}
