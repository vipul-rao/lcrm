<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('organization_id');
            $table->integer('company_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->string('quotations_number');
            $table->integer('qtemplate_id')->nullable();
            $table->date('date')->nullable();
            $table->date('exp_date')->nullable();
            $table->string('payment_term');
            $table->integer('sales_person_id')->nullable();
            $table->integer('sales_team_id')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->string('status');
            $table->float('total', 10, 0)->nullable();
            $table->float('tax_amount', 10, 0)->nullable();
            $table->float('grand_total', 10, 0)->nullable();
            $table->float('discount', 10, 0)->nullable();
            $table->float('final_price', 10, 0)->nullable();
            $table->integer('is_delete_list');
            $table->integer('is_converted_list');
            $table->integer('is_quotation_invoice_list');
            $table->integer('opportunity_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('quotations');
    }
}
