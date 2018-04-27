<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id');
            $table->integer('organization_id');
            $table->integer('customer_id')->nullable();
            $table->integer('sales_person_id')->nullable();
            $table->integer('sales_team_id')->nullable();
            $table->integer('qtemplate_id')->nullable();
            $table->integer('is_delete_list')->nullable();
            $table->integer('company_id')->nullable();
            $table->string('invoice_number');
            $table->date('invoice_date');
            $table->date('due_date');
            $table->string('payment_term');
            $table->string('status');
            $table->float('total');
            $table->float('tax_amount');
            $table->float('grand_total');
            $table->float('unpaid_amount');
            $table->float('discount', 10, 0)->nullable();
            $table->float('final_price');
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
        Schema::drop('invoices');
    }
}
