<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code')->nullable()->unique();
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('brand_id')->nullable();
            $table->decimal('cost')->nullable();
            $table->decimal('price');
            $table->decimal('damage_price')->nullable();
            $table->text('details')->nullable();
            $table->string('image')->nullable();
            $table->integer('stock')->default(0);
            $table->integer('main_unit_stock')->default(0);
            $table->integer('sub_unit_stock')->default(0);
            $table->integer('total_sold')->default(0);

            $table->integer('main_unit_id');
            $table->integer('sub_unit_id')->nullable();
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
        Schema::dropIfExists('products');
    }
}
