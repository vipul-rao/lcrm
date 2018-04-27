<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductVariantsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id');
            $table->string('attribute_name');
            $table->text('product_attribute_value');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('product_variants');
    }
}
