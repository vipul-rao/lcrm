<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSalesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('organization_id');
            $table->integer('quotation_id')->nullable();
            $table->integer('company_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('qtemplate_id')->nullable();
            $table->string('sale_number');
            $table->date('date');
            $table->date('exp_date');
            $table->string('payment_term');
            $table->integer('sales_person_id')->nullable();
            $table->integer('sales_team_id')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->string('status');
            $table->float('total');
            $table->float('tax_amount');
            $table->float('grand_total');
            $table->float('discount', 10, 0)->nullable();
            $table->float('final_price');
            $table->integer('is_delete_list')->nullable();
            $table->integer('is_invoice_list')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('sales_orders');
    }
}
