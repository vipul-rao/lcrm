<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQtemplatesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('qtemplates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('organization_id');
            $table->string('quotation_template');
            $table->integer('quotation_duration')->nullable();
            $table->boolean('immediate_payment')->default(1);
            $table->text('terms_and_conditions')->nullable();
            $table->float('total');
            $table->float('tax_amount');
            $table->float('grand_total');
            $table->integer('user_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('qtemplates');
    }
}
