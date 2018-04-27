<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInvoicesProductsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('invoices_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id');
            $table->integer('product_id');
            $table->integer('quantity');
            $table->float('price');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('invoices_products');
    }
}
