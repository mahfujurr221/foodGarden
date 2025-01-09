<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountToAccountTransectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_to_account_transections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('from')->nullable();
            $table->integer('to')->nullable();
            $table->integer('owner_id')->nullable();
            $table->decimal('amount', 12, 2);
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
        Schema::dropIfExists('account_to_account_transections');
    }
}
