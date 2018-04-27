<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVatNumberToInvoiceReceivePayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_receive_payments', function (Blueprint $table) {
            $table->float('vat_amount', 10, 0)->nullable();
            $table->string('vat_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_receive_payments', function (Blueprint $table) {
            $table->dropColumn('vat_amount');
            $table->dropColumn('vat_number');
        });
    }
}
