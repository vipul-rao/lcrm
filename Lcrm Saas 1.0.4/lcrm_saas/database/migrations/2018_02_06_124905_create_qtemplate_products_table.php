<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQtemplateProductsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('qtemplate_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('qtemplate_id');
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
        Schema::drop('qtemplate_products');
    }
}
