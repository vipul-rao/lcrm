<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInvoiceReceivePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('invoice_receive_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('organization_id');
            $table->integer('company_id')->nullable();
            $table->integer('invoice_id');
            $table->dateTime('payment_date');
            $table->string('payment_method');
            $table->float('payment_received');
            $table->string('payment_number');
            $table->string('paykey')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('invoice_receive_payments');
    }
}
