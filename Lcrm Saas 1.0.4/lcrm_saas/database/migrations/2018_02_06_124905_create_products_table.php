<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('organization_id');
            $table->string('product_name');
            $table->string('product_image')->nullable();
            $table->integer('category_id');
            $table->string('product_type');
            $table->string('status');
            $table->integer('quantity_on_hand');
            $table->integer('quantity_available');
            $table->float('sale_price');
            $table->text('description')->nullable();
            $table->text('description_for_quotations')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('products');
    }
}
