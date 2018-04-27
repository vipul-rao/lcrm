<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSalesOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('sales_order_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id');
            $table->integer('saleorder_id');
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
        Schema::drop('sales_order_products');
    }
}
