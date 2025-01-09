<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('due_collections', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->integer('due_by');
            $table->integer('payment_id')->nullable();
            $table->integer('pos_id')->nullable();
            $table->integer('direct_transection')->default(0);
            $table->integer('brand_id')->nullable();
            $table->date('last_due_date');
            $table->date('committed_due_date');
            $table->integer('amount')->default(0);
            $table->integer('paid')->default(0);
            $table->integer('due')->default(0);
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('due_collections');
    }
};
