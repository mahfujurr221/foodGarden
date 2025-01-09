<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_damage_items', function (Blueprint $table) {
            $table->id();
            $table->date('adjust_date');
            $table->unsignedInteger('estimate_id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('qty');
            $table->unsignedInteger('rate');
            $table->unsignedInteger('total');
            $table->tinyInteger('collection_status')->default(0);
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
        Schema::dropIfExists('order_damage_items');
    }
};