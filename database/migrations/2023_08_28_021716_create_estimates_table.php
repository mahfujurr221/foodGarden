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
        Schema::create('estimates', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('customer_id')->nullable();
            $table->unsignedInteger('brand_id')->nullable();
            $table->date('estimate_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->integer('estimate_by')->nullable();
            $table->string('estimate_number')->nullable();
            $table->decimal('total', 12, 2)->default(0); //total product price
            $table->decimal('receivable', 12, 2)->nullable(); //receivable after discount
            $table->decimal('final_receivable', 12, 2)->default(0); //after return -> receivable
            $table->text('note')->nullable();
            $table->boolean('convert_status')->default(0);
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('estimates');
    }
};