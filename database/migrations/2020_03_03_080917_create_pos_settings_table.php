<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pos_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('company');
            $table->string('logo')->nullable();
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            // $table->string('page_link')->nullable();
            // $table->string('website')->nullable();
            $table->string('header_text')->nullable();
            $table->string('footer_text')->nullable();
            // $table->boolean('sale_over_sotck')->default(0);
            $table->enum('invoice_type',['a4','a4-2','a4-3','pos','pos-2','pos-3']);
            $table->enum('invoice_logo_type', ['Logo', 'Name', 'Both'])->default('Logo');
            $table->enum('barcode_type',['single','a4']);
            $table->integer('low_stock')->default(10);
            $table->boolean('dark_mode')->default(0);
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
        Schema::dropIfExists('pos_settings');
    }
}
